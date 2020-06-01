<?php

namespace App\Console\Commands;

use App\Imports\ProductImport;
use App\Jobs\ImportJob;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Excel importing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $fileName = 'catalog_for_test.xlsx';

    protected $itemsPerPage = 500;

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
        ini_set('max_execution_time', '10');
        $collection = $this->getData()[0];
        $report_id = rand(1, 1000000);
        $this->import($collection, $report_id);
        $this->info('Completed');
    }

    public function getData()
    {
        return Excel::toCollection(new ProductImport(), storage_path($this->fileName));
    }

    public function import($items, $report_id)
    {
        $progressBar = $this->output->createProgressBar(count($items)/$this->itemsPerPage);
        $page = 1;
        while ($page) {
            $itemsPack = $items->forPage($page, $this->itemsPerPage);

            if ($page == 1) {
                unset($itemsPack[0]);
            }

            if (count($itemsPack) == 0) {
                break;
            }

            $job = new ImportJob([
                'items' => $itemsPack,
                'report_id' => $report_id
                ]);

            dispatch($job);
            $page++;

            $progressBar->advance();
        }
        $progressBar->finish();
    }
}
