<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\InventoryImportRequest;
use App\Http\Requests\Validations\InventoryUpdateRequest;
use App\Jobs\ProcessInventoryCsvBulkUpdate as ProcessUpdate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Inventory;

class InventoryBulkUpdateController extends Controller
{
    /**
     * Show update form
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        return view('admin.inventory._update_form');
    }

    /**
     * Update the csv file and generate the review table
     *
     * @param  InventoryUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(InventoryUpdateRequest $request)
    {
        $path = $request->file('inventories')->getRealPath();
        $records = array_map('str_getcsv', file($path));

        // Validations check for csv_import_limit
        if ((count($records) - 1) > get_csv_import_limit()) {
            $err = (new MessageBag)->add('error', trans('validation.update_rows', ['rows' => get_csv_import_limit()]));

            return back()->withErrors($err);
        }

        // Get field names from header column
        $fields = array_map('strtolower', $records[0]);

        // Remove the header column
        array_shift($records);

        $rows = [];
        foreach ($records as $record) {
            if (count($fields) != count($record)) {
                $err = (new MessageBag)->add('error', trans('validation.csv_update_invalid_data'));

                return back()->withErrors($err);
            }

            // Decode unwanted html entities
            $record =  array_map("html_entity_decode", $record);

            // Set the field name as key
            $temp = array_combine($fields, $record);

            // Get the clean data
            $rows[] = clear_encoding_str($temp);
        }

        return view('admin.inventory.update_review', compact('rows'));
    }

    /**
     * Perform import action
     *
     * @param  InventoryImportRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function import(InventoryImportRequest $request)
    {
        if (config('app.demo') == true) {
            return redirect()->route('admin.stock.inventory.index')
                ->with('warning', trans('messages.demo_restriction'));
        }

        if (config('queue.default') == 'sync') {
            $failed_rows = ProcessUpdate::dispatchSync(Auth::user(), $request->input('data'));

            if (is_array($failed_rows) && !empty($failed_rows)) {
                return view('admin.inventory.import_failed', compact('failed_rows'));
            }

            return redirect()->route('admin.stock.inventory.index')
                ->with('success', trans('messages.imported', ['model' => trans('app.model.inventory')]));
        }

        ProcessUpdate::dispatch(Auth::user(), $request->input('data'));

        return redirect()->route('admin.stock.inventory.index')
            ->with('global_notice', trans('messages.csv_import_process_started'));
    }

    /**
     * downloadTemplate
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadTemplate()
    {
        //$pathToFile = public_path('csv_templates/inventories_update.csv');
        $all_inventories = Inventory::mine()->get(['title','sale_price','slug','stock_quantity']);

        return (new FastExcel($all_inventories))->configureCsv(',','"','UTF-8')->download('inventories.csv');
    }

    /**
     * [downloadFailedRows]
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse A FastExcel response or an Excel file
     */
    public function downloadFailedRows(Request $request)
    {
        foreach ($request->input('data') as $row) {
            $data[] = unserialize($row);
        }

        $path = storage_path('failed_rows.xlsx');

        return (new FastExcel(collect($data)))->download($path);
    }
}
