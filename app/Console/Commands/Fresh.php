<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Fresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incevio:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the application by clear all files, cache, drop all tables, re-run migrations, seed the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('config:clear');

        $this->call('incevio:clear-storage');

        $this->call('cache:clear');

        $this->call('migrate:fresh', ['--force' => true]);

        $this->call('db:seed', ['--force' => true]);

        $this->info('Database is ready!');
    }
}
