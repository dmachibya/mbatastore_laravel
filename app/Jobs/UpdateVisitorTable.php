<?php

namespace App\Jobs;

use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateVisitorTable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ip_address;
    // protected $request;

    /**
     * Create a new job instance.
     *
     * @param  User  $merchant
     * @param  string  $request
     * @return void
     */
    public function __construct($ip_address)
    {
        // $this->request = $request;
        $this->ip_address = $ip_address;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $ip = '103.49.170.142'; //Temp for test

        $visitor = Visitor::withTrashed()->find($this->ip_address);

        if (!$visitor) {
            $visitor = new Visitor;
            $visitor->ip = $this->ip_address;
            $visitor->hits = 1;
            $visitor->page_views = 1;

            return $visitor->save();
        }

        // Increase the hits value if this visit is the first visit for today
        if ($visitor->updated_at->lt(Carbon::today())) {
            $visitor->hits++;
        }

        $visitor->page_views++;

        $visitor->save();
    }
}
