<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class Walkers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'walkers:minute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disables walkers every after one minute.';

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
        DB::table('walker')
              ->update(['is_active' => '0']);

        $this->info('All drivers offline');
    }

}
