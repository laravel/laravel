<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Clearall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform all clear functions of other artisan commands: cache:clear, config:clear, debugbar:clear, route:clear, view:clear';

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
        //
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('debugbar:clear');
        $this->call('route:clear');
        $this->call('view:clear');

    }
}
