<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class SiteDisable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:disable {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command enable/disable site status. active or inactive';

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
     * @return int
     */
    public function handle()
    {
        Setting::set('site_status', $this->argument('value'));
        return Command::SUCCESS;
    }
}
