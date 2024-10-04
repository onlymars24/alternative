<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SitemapService;

class RegenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regenerate:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // SitemapService::add('http://localhost:5173/автобус/Новосибирск/Барнаул', 'dayly');
        return Command::SUCCESS;
    }
}
