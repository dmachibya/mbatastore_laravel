<?php

namespace App\Jobs;

use App\Models\Inventory;
use App\Models\User;
use App\Notifications\Inventory\ProcessedCsvImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rap2hpoutre\FastExcel\FastExcel;
use Exception;

class ProcessInventoryCsvBulkUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 1200;

    public $user;

    private $csv_data;

    private $failed_list = [];

    private $products = [];

    private $success_counter;

    private $failed_file_path = '';

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $csv_data = [])
    {
        $this->user = $user;
        $this->csv_data = $csv_data;
        $this->success_counter = 0;
        $this->failed_list = [];

        ini_set('max_execution_time', 0); // Set unlimited time
        ini_set('max_input_vars', 100000); // Set a larger value
    }

    /**
     * Execute the job.
     *
     * @return array $failed_rows
     */
    public function handle()
    {
        foreach ($this->csv_data as $row) {
            $data = unserialize($row);

            // Invalid data
            if (!is_array($data)) continue;

            // Ignore if required info is not given
            if (!verifyRequiredDataForBulkUpload($data, 'inventory')) {
                $this->pushIntoFailed($data, trans('help.missing_required_data'));
                continue;
            }

            // Ignore if the slug is exist in the database
            $inventory = Inventory::where('slug', $data['slug'])->first();

            if ($inventory) {
                $inventory->update([
                    'title' =>  $data['title'] ?? $inventory->title,
                    'stock_quantity' => $data['stock_quantity'] ?? $inventory->quantity,
                    'sale_price' =>  $data['sale_price'] ?? $inventory->price,
                ]);

                $this->success_counter++; // Increase the counter for successful import
            } else {
                $failed_rows = $this->getFailedList();
            }
        }

        // When the job processing on current request cycle
        if (config('queue.default') != 'sync' && !empty($failed_rows)) {
            $this->failed_file_path = $this->createAttachmentWithFailedRows();
            $this->user->notify(new ProcessedCsvImport($failed_rows, $this->success_counter, $this->failed_file_path));
        }

        return $failed_rows;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
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
     * create attachment with failed data
     *
     * @param Excel $excel
     */
    public function createAttachmentWithFailedRows()
    {
        $data = [];

        foreach ($this->getFailedList() as $row) {
            $data[] = $row;
        }

        $path = storage_path('failed_rows.xlsx');

        return (new FastExcel(collect($data)))->export($path);
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
