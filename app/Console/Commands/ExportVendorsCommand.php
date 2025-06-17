<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExportVendorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:export {exportId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a vendor export asynchronously';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $exportId = (int) $this->argument('exportId');

        $export = \App\Models\VendorExport::find($exportId);
        if (! $export) {
            $this->error('Export record not found');
            return Command::FAILURE;
        }

        try {
            $fileName = 'vendors_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            $filePath = 'uploads/report/vendor/' . $fileName;

            $filters = [
                'range_start' => $export->range_start,
                'range_end'   => $export->range_end,
            ];

            \Maatwebsite\Excel\Facades\Excel::store(
                new \App\Exports\VendorsExport($filters),
                $filePath,
                'public'
            );

            $export->update([
                'status'    => 'completed',
                'file_name' => $fileName,
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $export->update(['status' => 'failed']);
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
