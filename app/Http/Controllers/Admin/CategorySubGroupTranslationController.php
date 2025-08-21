<?php

namespace App\Http\Controllers\Admin;

use App\Models\CategorySubGroup;
use App\Http\Controllers\Controller;
use App\Helpers\ListHelper;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Rap2hpoutre\FastExcel\FastExcel;

class CategorySubGroupTranslationController extends Controller
{
    private $failed_list = [];

    // Fields or columns that must be present in each row.
    private $required_fields = ['slug', 'lang' ,'name', 'description'];

    /**
     * Display the translation form for a specific category sub group.
     *
     * @param CategorySubGroup $categorySubGroup The category sub group instance.
     * @param string $selected_language The selected language for the translation.
     * @return \Illuminate\View\View The translation form view.
     */
    public function showTranslationForm(CategorySubGroup $categorySubGroup, string $selected_language)
    {
        $available_languages = ListHelper::availableTranslationLocales();
        
        if (!$available_languages->count()) {
            return back()->with('warning', trans('messages.no_translation_available'));
        }

        if ($selected_language == config('system_settings.default_language')) {
            return redirect()->route('admin.catalog.categorySubGroup.translate.form',['categorySubGroup' =>$categorySubGroup,'language' => $available_languages->first()->code]);
        }

        if (!$available_languages->count()) {
            return back()->with('warning', trans('messages.no_translation_available'));
        }

        $categorySubGroup_translation = $categorySubGroup->translations()->where('lang', $selected_language)->firstOrNew([
            'category_sub_group_id' => $categorySubGroup->id,
            'lang' => $selected_language,
            'translation' => []
        ]);

        return view('admin.category.subGroup._translation', compact('categorySubGroup', 'categorySubGroup_translation', 'available_languages', 'selected_language'));
    }

    /**
     * Store the translation for an categorySubGroup item.
     *
     * @param  CategorySubGroup  $categorySubGroup
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTranslation(CategorySubGroup $categorySubGroup, Request $request)
    {
        $existing_translation = $categorySubGroup->hasTranslation($request->lang);

        $categorySubGroup_translation = $categorySubGroup->translations()->where('lang',$request->lang)->firstOrNew([
            'category_sub_group_id' => $categorySubGroup->id,
            'lang' => $request->lang,
        ]);
        
        $categorySubGroup_translation->translation = [
            'name' => $request->input('name'),
        ];
        
        $categorySubGroup_translation->save();

        return back()->with('success', trans($existing_translation ? 'messages.updated' : 'messages.created', ['model' => 'CategorySubGroup Translation']));
    }

    /**
     * Display the form for bulk translation of categorySubGroup data.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showBulkUploadForm()
    {
        return view('admin.category.subGroup._bulk_translation_form');
    }

    public function uploadBulkTranslation(Request $request)
    {
        $this->validate($request, [
            'categorySubGroupTranslations' => 'required|file|mimes:csv',
        ]);
        
        $path = $request->file('categorySubGroupTranslations')->getRealPath();
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

        return view('admin.category.subGroup._translation_bulk_upload_review', compact('rows'));
    }

    /**
     * Imports bulk translations for categories.
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
                $categorySubGroup = CategorySubGroup::where('slug', $data['slug'])->first();

                if (!$categorySubGroup) {
                    $this->pushIntoFailed($data, trans('help.category_not_found'));
                }

                $categorySubGroup_translation = $categorySubGroup->translations()->where('lang', $data['lang'])->firstOrNew([
                    'category_sub_group_id' => $categorySubGroup->id,
                    'lang' => $data['lang'],
                ]);

                $categorySubGroup_translation->translation = [
                    'name' => $data['name'],
                ];

                $categorySubGroup_translation->save();

            } catch (\Exception $error) {
                $this->pushIntoFailed($data, $error->getMessage());

                \Log::error($error);
                continue;
            }
        }
        $request->session()->flash('success', trans('messages.imported', ['model' => trans('CategorySubGroup Translation')]));

        $failed_rows = $this->getFailedList();

        if (empty($failed_rows)) {
            return redirect()->route('admin.catalog.category.subGroup.index');
        }

        return view('admin.category.subGroup._translation_import_failed', compact('failed_rows'));
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
     * Download the template file for category sub group translations.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse the template csv file
     */
    public function downloadTemplate()
    {
        $available_languages = ListHelper::availableTranslationLocales();
        $default_language = config('system_settings.default_language');

        $category_sub_groups = CategorySubGroup::get(['id', 'name', 'slug', 'description']);

        $rows = [];
        foreach($category_sub_groups as $category_sub_group){
            $rows[] = [
                'slug' => $category_sub_group->slug,
                'lang' => $default_language,
                'name' => $category_sub_group->name,
            ];

            foreach($available_languages as $language){
                $category_sub_group_translation = $category_sub_group->translations()->where('lang', $language->code)->first();

                $rows[] = [
                    'slug' => $category_sub_group->slug,
                    'lang' => $language->code,
                    'name' => $category_sub_group_translation->translation['name'] ?? '',
                ];
            }
        }

        return (new FastExcel(collect($rows)))->configureCsv(',','"','UTF-8')->download('categorySubGroupTranslations.csv');
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