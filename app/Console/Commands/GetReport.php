<?php

namespace App\Console\Commands;

use App\Models\ImportReport;
use Illuminate\Console\Command;

class GetReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get last import report';

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
        $duplicateFields = 0;
        $duplicateNull = 0;
        $sum = 0;

        $report = ImportReport::latest()->first();
        $lastReports = ImportReport::where('report_id', $report->report_id)->get();
        foreach ($lastReports as $report) {
            $duplicateFields += $report->duplicate_fields;
            $duplicateNull += $report->duplicate_null;
            $sum += $report->sum;
        }

        $this->info('Last import report');
        $this->info('Duplicate products : '.$duplicateFields);
        $this->info('Incorrect product fields : '.$duplicateNull);
        $this->info('Uploaded products : '.$sum);
    }
}
