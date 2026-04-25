<?php

namespace App\Console\Commands;

use App\Services\ApiFetcherService;
use Illuminate\Console\Command;

class ApiFetchCommand extends Command
{
    protected $signature = 'api:fetch 
                            {--entity=sales : Entity type: sales|orders|stocks|incomes} 
                            {--date-from= : Start date (Y-m-d). Default: 1970-01-01} 
                            {--date-to= : End date (Y-m-d). Default: today}';

    protected $description = 'Fetch data from external API and store in raw format';

    protected ApiFetcherService $fetcher;

    public function __construct(ApiFetcherService $fetcher)
    {
        parent::__construct();
        $this->fetcher = $fetcher;
    }

    public function handle()
    {
        $entity = $this->option('entity');
        $dateFrom = $this->option('date-from') ?: '1970-01-01';
        $dateTo = $this->option('date-to') ?: now()->format('Y-m-d');

        $this->info("Fetching '{$entity}' from {$dateFrom} to {$dateTo}...");

        try {
            $this->fetcher->fetchEntity($entity, $dateFrom, $dateTo);
            $this->info('Successfully completed.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}