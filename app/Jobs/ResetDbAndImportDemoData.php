<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;

class ResetDbAndImportDemoData
{
    use Dispatchable;

    private $action;

    /**
     * Create a new job instance.
     *
     * @param reset/clean $action
     */
    public function __construct($action = 'reset')
    {
        if (in_array($action, ['reset,clean'])) {
            $this->action = $action;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->action == 'clean') {
            Artisan::call('incevio:fresh');
        } else {
            Artisan::call('incevio:reset-demo --sql');
        }

        Artisan::call('up');
    }
}
