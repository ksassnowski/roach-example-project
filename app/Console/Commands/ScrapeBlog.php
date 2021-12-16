<?php

namespace App\Console\Commands;

use App\Spiders\FussballdatenSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;

class ScrapeBlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:fussballdaten';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Roach::startSpider(FussballdatenSpider::class);

        return 0;
    }
}
