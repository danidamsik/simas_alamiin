<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceReportExport implements FromArray, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithTitle
{
    /**
     * @param  array<string, mixed>  $report
     */
    public function __construct(private readonly array $report)
    {
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        $rows = $this->report['rows']
            ->values()
            ->map(fn (array $row, int $index) => [
                $index + 1,
                $row['nama'],
                $row['nis'],
                $row['hadir'],
                $row['izin'],
                $row['sakit'],
                $row['alfa'],
                $row['persentase'].'%',
            ])
            ->all();

        $rows[] = [
            '',
            'TOTAL',
            '',
            $this->report['totals']['hadir'],
            $this->report['totals']['izin'],
            $this->report['totals']['sakit'],
            $this->report['totals']['alfa'],
            '',
        ];

        return $rows;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['No', 'Nama', 'NIS', 'Total Hadir', 'Izin', 'Sakit', 'Alfa', 'Persentase Kehadiran'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
            ],
        ];
    }

    /**
     * @return array<class-string, callable>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->report['rows']->count() + 2;

                $sheet->freezePane('A2');

                foreach (range(2, max($lastRow - 1, 2)) as $row) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:H{$row}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('F9FAFB');
                    }
                }

                $sheet->getStyle("A{$lastRow}:H{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCFCE7']],
                ]);
            },
        ];
    }

    public function title(): string
    {
        return 'Rekap Absensi';
    }
}
