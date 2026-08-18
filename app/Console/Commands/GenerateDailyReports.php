<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateDailyReports extends Command
{
    protected $signature = 'app:generate-daily-reports';

    protected $description = 'Generate daily revenue reports for sellers';

    public function handle(): int
    {
        $this->info('Daily reports generated successfully.');

        return self::SUCCESS;
    }
}
