<?php
namespace App\Http\Controllers\Admin;

use App\Models\Manufacturer;
use App\Helpers\ListHelper;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Http\Controllers\Controller;

class ManufacturerTranslationController extends Controller
{
    private $failed_list = [];

    // Fields or columns that must be present in each row.
    private $required_fields = ['slug', 'lang' ,'name', 'description'];

    /**
     * Display the translation form for a specific manufacturer.
     *
     * @param Manufacturer $manufacturer The manufacturer instance.
     * @param string $selected_language The selected language for the translation.
     * @return \Illuminate\View\View The view for the translation form.
     */
    public function showTranslationForm(Manufacturer $manufacturer, string $selected_language)
    {
        $available_languages = ListHelper::availableTranslationLocales();
        
        if (!$available_languages->count()) {
            return back()->with('warning', trans('messages.no_translation_available'));
        }

        if ($selected_language == config('system_settings.default_language')) {
            return redirect()->route('admin.catalog.manufacturer.translate.form',['manufacturer' =>$manufacturer,'language' => $available_languages->first()->code]);
        }
        
        if (!$available_languages->count()) {
            return back()->with('warning', trans('messages.no_translation_available'));
        }

        $manufacturer_translation = $manufacturer->translations()->where('lang', $selected_language)->firstOrNew([
            'manufacturer_id' => $manufacturer->id,
            'lang' => $selected_language,
        ]);

        return view('admin.manufacturer._translation', compact('manufacturer', 'manufacturer_translation', 'available_languages', 'selected_language'));
    }

    /**
     * Store the translation for an manufacturer item.
     *
     * @param  Manufacturer  $manufacturer
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTranslation(Manufacturer $manufacturer, Request $request)
    {
        $existing_translation = $manufacturer->hasTranslation($request->lang);

        $manufacturer_translation = $manufacturer->translations()->where('lang',$request->lang)->firstOrNew([
            'manufacturer_id' => $manufacturer->id,
            'lang' => $request->lang,
        ]);
        
        $manufacturer_translation->translation = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];
        
        $manufacturer_translation->save();

        return back()->with('success', trans($existing_translation ? 'messages.updated' : 'messages.created', ['model' => 'Manufacturer Translation']));
    }

    /**
     * Display the form for bulk translation of manufacturer data.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showBulkUploadForm()
    {
        return view('admin.manufacturer._bulk_translation_form');
    }

    public function uploadBulkTranslation(Request $request)
    {
        $this->validate($request, [
            'manufacturerTranslations' => 'required|file|mimes:csv',
        ]);
        
        $path = $request->file('manufacturerTranslations')->getRealPath();
        $records = array_map('str_getcsv', file($path));
        
        // Validations check for csv_import_limit
        if (count($records) > get_csv_import_limit()) {
            $err = (new MessageBag)->add('error', trans('validation.upload_rows', ['rows' => get_csv_import_limit()]));

            return back()->withErrors($err);
        }

        // Get field names from header column
        $fields = array_map('strtolower', $records[0]);
        // Check if any column headers has been changed or missing.
        $missing_fields = array_diff($this->required_fields, $fields);
        if (!empty($missing_fields)) {
            $err = (new MessageBag)->add('error', trans('validation.csv_upload_invalid_data'));

            return back()->withErrors($err);
        }

        // Remove the header column
        array_shift($records);

        $rows = [];
        foreach ($records as $record) {
            if (count($fields) != count($record)) {
                $err = (new MessageBag)->add('error', trans('validation.csv_upload_invalid_data'));

                return back()->withErrors($err);
            }

            // Decode unwanted html entities
            $record =  array_map("html_entity_decode", $record);

            $search = array('&#39;');
            $replace = array(' \' ');
            $record = str_replace($search, $replace, $record);

            // Set the field name as key
            $record = array_combine($fields, $record);

            // Get the clean data
            $rows[] = clear_encoding_str($record);
        }

        return view('admin.manufacturer._translation_bulk_upload_review', compact('rows'));
    }

    /**
     * Imports bulk translations for manufacturers.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     * @return \Illuminate\View\View The response object or the view.
     */
    public function importBulkTranslation(Request $request)
    {
        // Reset the Failed list
        $this->failed_list = [];

        $records = $request->input('data');
        foreach ($records as $row) {
            $data = unserialize($row);

            if ($this->dataHasMissingField($data)) {
                $this->pushIntoFailed($data, trans('help.missing_required_data'));
                continue;
            }

            // perform uploading to database below
            try {
                $manufacturer = Manufacturer::where('slug', $data['slug'])->first();

                if (!$manufacturer) {
                    $this->pushIntoFailed($data, trans('help.manufacturer_not_found'));
                }

                $translation = [
                    'name' => $data['name'],
                    'description' => $data['description'],
                ];

                $manufacturer_translation = $manufacturer->translations()->where('lang', $data['lang'])->firstOrNew([
                    'manufacturer_id' => $manufacturer->id,
                    'lang' => $data['lang'],
                ]);

                $manufacturer_translation->translation = $translation;
                $manufacturer_translation->save();

            } catch (\Exception $error) {
                $this->pushIntoFailed($data, $error->getMessage());

                \Log::error($error);
                continue;
            }
        }
        $request->session()->flash('success', trans('messages.imported', ['model' => trans('Manufacturer Translation')]));

        $failed_rows = $this->getFailedList();

        if (empty($failed_rows)) {
            return redirect()->route('admin.catalog.manufacturer.index');
        }

        return view('admin.manufacturer._translation_import_failed', compact('failed_rows'));
    }

    /**
     * Check if the given data has any missing fields.
     *
     * @param array|null $data The data to be checked.
     * @return bool Returns true if the data has missing fields, false otherwise.
     */
    private function dataHasMissingField($data)
    {
        if (!isset($data) || !is_array($data)) {
            return true; // Data is not set or not an array
        }

        foreach ($this->required_fields as $field) {
            if (!isset($data[$field])) {
                return true; // When Field is missing
            }
        }

        return false;
    }

    /**
     * Download the template file for manufacturer translations.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse the template csv file
     */
    public function downloadTemplate()
    {
        $available_languages = ListHelper::availableTranslationLocales();
        $default_language = config('system_settings.default_language');

        $manufacturers = Manufacturer::get(['id', 'name', 'slug', 'description']);

        $rows = [];
        foreach($manufacturers as $manufacturer){
            $rows[] = [
                'slug' => $manufacturer->slug,
                'lang' => $default_language,
                'name' => $manufacturer->name,
                'description' => $manufacturer->description,
            ];

            //row for each available language
            foreach($available_languages as $language){
                $manufacturer_translation = $manufacturer->translations()->where('lang', $language->code)->first();
                $rows[] = [
                    'slug' => $manufacturer->slug,
                    'lang' => $language->code,
                    'name' => $manufacturer_translation->translation['name'] ?? '',
                    'description' => $manufacturer_translation->translation['description'] ?? '',
                ];
            }
        }

        return (new FastExcel(collect($rows)))->configureCsv(',','"','UTF-8')->download('manufacturerTranslations.csv');
    }

    /**
     * Download the failed rows as an Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadFailedRows(Request $request)
    {
        foreach ($request->input('data') as $row) {
            $data[] = unserialize($row);
        }

        return (new FastExcel(collect($data)))->download('failed_rows.xlsx');
    }

    /**
     * Push New value Into Failed List
     *
     * @param  array  $data
     * @param  string $reason
     * @return void
     */
    private function pushIntoFailed(array $data, $reason = null)
    {
        $row = [
            'data' => $data,
            'reason' => $reason,
        ];

        array_push($this->failed_list, $row);
    }

    /**
     * Return the failed list
     *
     * @return array
     */
    private function getFailedList()
    {
        return $this->failed_list;
    }
}
