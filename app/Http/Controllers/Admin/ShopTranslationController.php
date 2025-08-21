<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\ListHelper;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Rap2hpoutre\FastExcel\FastExcel;

class ShopTranslationController extends Controller
{
    private $failed_list = [];

    // Fields or columns that must be present in each row.
    private $required_fields = ['slug', 'lang' ,'name', 'description'];

    /**
     * Display the translation form for a specific shop and language.
     *
     * @param Shop $shop The shop instance.
     * @param string $selected_language The selected language for the translation.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View The translation form view.
     */
    public function showTranslationForm(Shop $shop, string $selected_language)
    {
        if (!$this->userHasTranslationPermission($shop)) {
            return back()->with(['error' => trans('messages.permission.denied')]);
        }

        $available_languages = ListHelper::availableTranslationLocales();

        if (!$available_languages->count()) {
            return back()->with('warning', trans('messages.no_translation_available'));
        }

        if ($selected_language == config('system_settings.default_language')) {
            return redirect()->route('admin.vendor.shop.translate.form',['shop' =>$shop,'language' => $available_languages->first()->code]);
        }

        if (!$available_languages->count()) {
            return back()->with('warning', trans('messages.no_translation_available'));
        }

        $shop_translation = $shop->translations()->where('lang', $selected_language)->firstOrNew([
            'shop_id' => $shop->id,
            'lang' => $selected_language,
        ]);

        return view('admin.shop._translation', compact('shop', 'shop_translation', 'available_languages', 'selected_language'));
    }

    /**
     * Store the translation for an shop item.
     *
     * @param  Shop  $shop
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTranslation(Shop $shop, Request $request)
    {
        if (!$this->userHasTranslationPermission($shop)) {
            return back()->with(['error' => trans('messages.permission.denied')]);
        }

        $existing_translation = $shop->hasTranslation($request->lang);

        $shop_translation = $shop->translations()->where('lang',$request->lang)->firstOrNew([
            'shop_id' => $shop->id,
            'lang' => $request->lang,
        ]);
        
        $shop_translation->translation = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];
        
        $shop_translation->save();

        return back()->with('success', trans($existing_translation ? 'messages.updated' : 'messages.created', ['model' => 'Shop Translation']));
    }

    /**
     * Display the form for bulk translation of shop data.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showBulkUploadForm()
    {
        return view('admin.shop._bulk_translation_form');
    }

    public function uploadBulkTranslation(Request $request)
    {
        $this->validate($request, [
            'shopTranslations' => 'required|file|mimes:csv',
        ]);
        
        $path = $request->file('shopTranslations')->getRealPath();
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

        return view('admin.shop._translation_bulk_upload_review', compact('rows'));
    }

    /**
     * Imports bulk translations for shops.
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
                $shop = Shop::where('slug', $data['slug'])->first();

                if (!$shop) {
                    $this->pushIntoFailed($data, trans('help.shop_not_found'));
                }

                $translation = [
                    'name' => $data['name'],
                    'description' => $data['description'],
                ];

                $shop_translation = $shop->translations()->where('lang', $data['lang'])->firstOrNew([
                    'shop_id' => $shop->id,
                    'lang' => $data['lang'],
                ]);

                $shop_translation->translation = $translation;
                $shop_translation->save();

            } catch (\Exception $error) {
                $this->pushIntoFailed($data, $error->getMessage());

                \Log::error($error);
                continue;
            }
        }
        $request->session()->flash('success', trans('messages.imported', ['model' => trans('Shop Translation')]));

        $failed_rows = $this->getFailedList();

        if (empty($failed_rows)) {
            return redirect()->route('admin.vendor.shop.index');
        }

        return view('admin.shop._translation_import_failed', compact('failed_rows'));
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
     * Download the template file for shop translations.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse the template csv file
     */
    public function downloadTemplate()
    {
        $available_languages = ListHelper::availableTranslationLocales();
        $default_language = config('system_settings.default_language');

        $shops = Shop::get(['id', 'name', 'slug', 'description']);

        $rows = [];
        foreach($shops as $shop){
            $rows[] = [
                'slug' => $shop->slug,
                'lang' => $default_language,
                'name' => $shop->name,
                'description' => $shop->description,
            ];

            //row for each available language
            foreach($available_languages as $language){
                $shop_translation = $shop->translations()->where('lang', $language->code)->first();
                $rows[] = [
                    'slug' => $shop->slug,
                    'lang' => $language->code,
                    'name' => $shop_translation->translation['name'] ?? '',
                    'description' => $shop_translation->translation['description'] ?? '',
                ];
            }
        }

        return (new FastExcel(collect($rows)))->configureCsv(',','"','UTF-8')->download('shopTranslations.csv');
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

    private function userHasTranslationPermission(Shop $shop)
    {
        if (\Auth::user()->id == $shop->owner_id || \Auth::user()->isSuperAdmin()) {
            return true;
        }

        return false;
    }
}