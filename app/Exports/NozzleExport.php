<?php

namespace App\Exports;
use App\Models\Nozzle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class NozzleExport implements FromCollection, WithHeadings
{
    protected  $stationid;

    public function __construct($stationid)
    {
        $this->stationid = $stationid;
    }

     public function collection()
    {
        return Nozzle::select('nozzle.NozzleNumber')
            ->join('dispenser', 'nozzle.DispenserID', '=', 'dispenser.DispenserID')
            ->where('dispenser.StationID', $this->stationid)
            ->get();
    }
    public function map($row): array
    {
        return [
            "'" . $row->NozzleNumber, // แคสต์ให้เป็น string เพื่อให้ Excel มองว่าเป็น text
        ];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,  // บังคับคอลัมน์ A เป็นข้อความ
        ];
    }
    public function headings(): array
    {
        return ['Nozzle Number'];
    }
}
