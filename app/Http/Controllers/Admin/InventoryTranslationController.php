<?php
namespace App\Http\Controllers\Admin;

use App\Models\Inventory;
use App\Helpers\ListHelper;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Http\Controllers\Controller;

class InventoryTranslationController extends Controller
{
    private $failed_list = [];

    // Fields or columns that must be present in each row.
    private $required_fields = ['slug', 'lang' ,'title', 'description', 'condition_note', 'key_features'];

    /**
     * Display the translation form for a specific inventory item.
     *
     * @param Inventory $inventory The inventory item for which the translation form is being displayed.
     * @param string $selected_language The selected language for the translation.
     * @return \Illuminate\View\View The view for the translation form.
     */
    public function showTranslationForm(Inventory $inventory, string $selected_language)
    {
        $available_languages = ListHelper::availableTranslationLocales();
        
        if (!$available_languages->count()) {
            return back()->with('warning', trans('messages.no_translation_available'));
        }

        if ($selected_language == config('system_settings.default_language')) {
            return redirect()->route('admin.stock.inventory.translation.form',['inventory' =>$inventory,'language' => $available_languages->first()->code]);
        }
        
        if (!$available_languages->count()) {
            return back()->with('warning', trans('messages.no_translation_available'));
        }

        $inventory_translation = $inventory->translations()->where('lang', $selected_language)->firstOrNew([
            'inventory_id' => $inventory->id,
            'lang' => $selected_language,
            'translation' => []
        ]);

        return view('admin.inventory._translation', compact('inventory', 'inventory_translation', 'available_languages', 'selected_language'));
    }

    /**
     * Store the translation for an inventory item.
     *
     * @param  Inventory  $inventory
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTranslation(Inventory $inventory, Request $request)
    {
        $existing_translation = $inventory->hasTranslation($request->lang);

        $inventory_translation = $inventory->translations()->where('lang',$request->lang)->firstOrNew([
            'inventory_id' => $inventory->id,
            'lang' => $request->lang,
        ]);
        
        $inventory_translation->translation = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'condition_note' => $request->input('condition_note'),
            'key_features' => $request->input('key_features')
        ];
        
        $inventory_translation->save();

        return back()->with('success', trans($existing_translation ? 'messages.updated' : 'messages.created', ['model' => 'Inventory Translation']));
    }

    /**
     * Display the bulk upload form for inventory translation.
     *
     * @return \Illuminate\View\View
     */
    public function showBulkUploadForm()
    {
        return view('admin.inventory._bulk_translation_form');
    }

    public function uploadBulkTranslation(Request $request)
    {
        $this->validate($request, [
            'inventoryTranslations' => 'required|file|mimes:csv',
        ]);
        
        $path = $request->file('inventoryTranslations')->getRealPath();
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

        return view('admin.inventory._translation_bulk_upload_review', compact('rows'));
    }

    /**
     * Imports bulk translations for inventory items.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing the data to import.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View The response after importing translations or the view for failed import.
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
                $inventory = Inventory::where('slug', $data['slug'])->first();

                if (!$inventory) {
                    $this->pushIntoFailed($data, trans('help.inventory_not_found'));
                }

                $translation = [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'condition_note' => $data['condition_note'],
                    'key_features' => $this->getKeyFeaturesFromString($data['key_features']),
                ];

                $inventory_translation = $inventory->translations()->where('lang', $data['lang'])->firstOrNew([
                    'inventory_id' => $inventory->id,
                    'lang' => $data['lang'],
                ]);

                $inventory_translation->translation = $translation;
                $inventory_translation->save();

            } catch (\Exception $error) {
                $this->pushIntoFailed($data, $error->getMessage());

                \Log::error($error);
                continue;
            }
        }
        $request->session()->flash('success', trans('messages.imported', ['model' => trans('Inventory Translation')]));

        $failed_rows = $this->getFailedList();

        if (empty($failed_rows)) {
            return redirect()->route('admin.stock.inventory.index');
        }

        return view('admin.inventory.translation_import_failed', compact('failed_rows'));
    }

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
     * Download the template file for inventory translations.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse the template csv file
     */
    public function downloadTemplate()
    {
        $available_languages = ListHelper::availableTranslationLocales();
        $default_language = config('system_settings.default_language');

        $inventories = Inventory::get(['id', 'title', 'slug', 'description', 'condition_note', 'key_features']);

        $rows = [];
        foreach($inventories as $inventory){
            $rows = [
                'slug' => $inventory->slug,
                'lang' => $default_language,
                'title' => $inventory->title,
                'description' => $inventory->description,
                'condition_note' => $inventory->condition_note,
                'key_features' => implode('##', $inventory->key_features)
            ];

            //row for each available language
            foreach($available_languages as $language){
                $inventory_translation = $inventory->translations()->where('lang', $language->code)->first();
                $rows[] = [
                    'slug' => $inventory->slug,
                    'lang' => $language->code,
                    'title' => $inventory_translation->translation['title'] ?? '',
                    'description' => $inventory_translation->translation['description'] ?? '',
                    'condition_note' => $inventory_translation->translation['condition_note'] ?? '',
                    'key_features' => $inventory_translation->translation['key_features'] ?? '',
                ];
            }
        }

        return (new FastExcel(collect($rows)))->configureCsv(',','"','UTF-8')->download('InventoryTranslations.csv');
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

    /**
     * Get the key features from the string. Where key features are separated by tow hash signs.
     */
    private function getKeyFeaturesFromString($key_features)
    {
        return explode('##', $key_features);
    }
}
