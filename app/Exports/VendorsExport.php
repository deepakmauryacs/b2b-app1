<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VendorsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return User::query()
            ->where('role', 'vendor')
            ->with('vendorProfile')
            ->withCount([
                'products as approved_products_count' => function ($q) {
                    $q->where('status', 'approved');
                },
                'products as pending_products_count' => function ($q) {
                    $q->where('status', 'pending');
                }
            ])
            ->when($this->filters['name'] ?? null, function ($q, $name) {
                $q->where('name', 'like', "%{$name}%");
            })
            ->when($this->filters['email'] ?? null, function ($q, $email) {
                $q->where('email', 'like', "%{$email}%");
            })
            ->when(isset($this->filters['status']) && $this->filters['status'] !== '', function ($q) {
                $q->where('status', (int) $this->filters['status']);
            })
            ->when($this->filters['gst_no'] ?? null, function ($q, $gstNo) {
                $q->whereHas('vendorProfile', function ($q2) use ($gstNo) {
                    $q2->where('gst_no', 'like', "%{$gstNo}%");
                });
            })
            ->when($this->filters['phone'] ?? null, function ($q, $phone) {
                $q->where('phone', 'like', "%{$phone}%");
            })
            ->orderBy('created_at', 'desc')
            ->when(isset($this->filters['range_start'], $this->filters['range_end']), function ($q) {
                $start = (int) $this->filters['range_start'];
                $end = (int) $this->filters['range_end'];
                $limit = $end - $start;
                $q->skip($start)->take($limit);
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Store Name',
            'GST No',
            'Pincode',
            'Address',
            'Status',
            'Is Verified',
            'Approved Products',
            'Pending Products',
            'Created At'
        ];
    }

    public function map($vendor): array
    {
        return [
            $vendor->id,
            $vendor->name,
            $vendor->email,
            $vendor->phone,
            $vendor->vendorProfile->store_name ?? 'N/A',
            $vendor->vendorProfile->gst_no ?? 'N/A',
            $vendor->vendorProfile->pincode ?? 'N/A',
            $vendor->vendorProfile->address ?? 'N/A',
            $vendor->status == 1 ? 'Active' : 'Inactive',
            $vendor->is_profile_verified == 1 ? 'Yes' : 'No',
            $vendor->approved_products_count,
            $vendor->pending_products_count,
            $vendor->created_at->format('d M Y H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFD9D9D9']
                ]
            ],
            // Set border for all cells
            'A1:M' . ($sheet->getHighestRow()) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
        ];
    }

    public function chunkSize(): int
    {
        return 500; // Process in chunks of 500 records
    }
}