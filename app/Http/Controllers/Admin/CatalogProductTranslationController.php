<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\ListHelper;
use Illuminate\Http\Request;
use App\Models\Product;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\MessageBag;

class CatalogProductTranslationController extends Controller
{
    private $failed_list = [];
    private $required_fields = ['slug', 'lang', 'name', 'description', 'brand'];

    /**
     * Display the translation form for a specific product and language.
     *
     * @param  Product  $product  The product instance.
     * @param  string  $selected_language  The selected language for translation.
     * @return \Illuminate\Contracts\View\View  The view for the translation form.
     */
    public function showTranslationForm(Product $product, string $selected_language)
    {
        $this->authorize('view', $product); // Check permission

        $available_languages = ListHelper::availableTranslationLocales();

        if (!$available_languages->count()) {
            return back()->with('warning', trans('messages.no_translation_available'));
        }

        if ($selected_language == config('system_settings.default_language')) {
            return redirect()->route('admin.catalog.product.translate.form', ['product' => $product, 'language' => $available_languages->first()->code]);
        }

        $product_translation = $product->translations()->where('lang', $selected_language)->firstOrNew([
            'product_id' => $product->id,
            'lang' => $selected_language,
        ]);

        return view('admin.product._translation', compact('product', 'product_translation', 'selected_language', 'available_languages'));
    }

    /**
     * Store the translation of a product.
     *
     * @param  Product  $product  The product instance.
     * @param  Request  $request  The request instance.
     * @return \Illuminate\Http\RedirectResponse  The redirect response.
     */
    public function storeTranslation(Product $product, Request $request)
    {
        $this->authorize('update', $product);

        $product_translation = $product->translations()->where('lang', $request->lang)->firstOrNew([
            'product_id' => $product->id,
            'lang' => $request->lang,
        ]);

        $product_translation->translation = [
            'name' => $request->name,
            'brand' => $request->brand,
            'description' => $request->description,
        ];

        $product_translation->save();

        return back()->with('success', trans('messages.created', ['model' => 'Product Translation']));
    }

    /**
     * Display the bulk upload form for product translations.
     *
     * @return \Illuminate\View\View
     */
    public function showBulkUploadForm()
    {
        return view('admin.product._bulk_translation_form');
    }

    /**
     * Uploads bulk translations from a CSV file and processes the data.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object that contains the uploaded file.
     * @return \Illuminate\Http\Response|\Illuminate\View\View The response or view for reviewing the uploaded data.
     */
    public function uploadBulkTranslation(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'productTranslations' => 'required|file|mimes:csv',
        ]);

        // Get the path of the uploaded file
        $path = $request->file('productTranslations')->getRealPath();

        // Read the CSV file and convert it into an array of records
        $records = array_map('str_getcsv', file($path));

        // Check if the number of records exceeds the CSV import limit
        if (count($records) > get_csv_import_limit()) {
            $err = (new MessageBag)->add('error', trans('validation.upload_rows', ['rows' => get_csv_import_limit()]));

            return back()->withErrors($err);
        }

        // Get the field names from the header column
        $fields = array_map('strtolower', $records[0]);

        // Check if any column headers have been changed or are missing
        $missing_fields = array_diff($this->required_fields, $fields);
        if (!empty($missing_fields)) {
            $err = (new MessageBag)->add('error', trans('validation.csv_upload_invalid_data'));

            return back()->withErrors($err);
        }

        // Remove the header column from the records array
        array_shift($records);

        $rows = [];
        foreach ($records as $record) {
            // Check if the number of fields in the record matches the number of fields in the header
            if (count($fields) != count($record)) {
                $err = (new MessageBag)->add('error', trans('validation.csv_upload_invalid_data'));

                return back()->withErrors($err);
            }

            // Decode unwanted HTML entities in the record
            $record =  array_map("html_entity_decode", $record);

            // Replace specific HTML entities in the record
            $search = array('&#39;');
            $replace = array(' \' ');
            $record = str_replace($search, $replace, $record);

            // Set the field name as the key in the record array
            $record = array_combine($fields, $record);

            // Get the clean data by removing any encoding issues
            $rows[] = clear_encoding_str($record);
        }

        // Return the view for reviewing the uploaded data
        return view('admin.product._translation_bulk_upload_review', compact('rows'));
    }
    /**
     * Imports bulk translations from CSV for catalog products.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View The response object or the view.
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
                $product = Product::where('slug', $data['slug'])->first();

                if (!$product) {
                    $this->pushIntoFailed($data, trans('help.inventory_not_found'));
                }

                $translation = [
                    'name' => $request->name,
                    'brand' => $request->brand,
                    'description' => $request->description,
                ];

                $inventory_translation = $product->translations()->where('lang', $data['lang'])->firstOrNew([
                    'inventory_id' => $product->id,
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
        $request->session()->flash('success', trans('messages.imported', ['model' => trans('Product Translation')]));

        $failed_rows = $this->getFailedList();

        if (empty($failed_rows)) {
            return redirect()->route('admin.catalog.product.index');
        }

        return view('admin.product.translation_import_failed', compact('failed_rows'));
    }

    /**
     * Checks if the given data array has any missing fields.
     *
     * @param array $data The data array to check.
     * @return bool Returns true if the data array has missing fields, false otherwise.
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
     * Download the template for product translations.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse the template csv file.
     */
    public function downloadTemplate()
    {
        $available_languages = ListHelper::availableTranslationLocales();
        $default_language = config('system_settings.default_language');

        $products = Product::get(['id', 'name', 'slug', 'description', 'brand']);

        $rows = [];
        foreach ($products as $product) {
            $rows[] = [
                'slug' => $product->slug,
                'lang' => $default_language,
                'name' => $product->name,
                'brand' => $product->brand,
                'description' => $product->description,
            ];

            foreach ($available_languages as $language) {
                $product_translation = $product->translations()->where('lang', $language->code)->first();

                $rows[] = [
                    'slug' => $product->slug,
                    'lang' => $language->code,
                    'name' => $product_translation->translation['name'] ?? '',
                    'brand' => $product_translation->translation['brand'] ?? '',
                    'description' => $product_translation->translation['description'] ?? '',
                ];
            }
        }

        return (new FastExcel(collect($rows)))->configureCsv(',', '"', 'UTF-8')->download('productTranslations.csv');
    }

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
