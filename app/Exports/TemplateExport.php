<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;


class TemplateExport implements FromCollection, WithHeadings, WithEvents
{
    public function headings(): array
    {
        return [
            'Kode Produk',
            'Nama Produk',
            'Harga Beli',
            'Harga Jual',
            'Kode Supplier',
          
        ];
    }

    public function collection()
    {
        // Mengembalikan koleksi kosong
        return collect([]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                
                $event->sheet->getStyle('1:100000')->getProtection()->setLocked(false);
                $sheet->getStyle('A1:XFD1')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
    
                $sheet->getProtection()->setSheet(true);
                
                $supplier = Supplier::pluck('kode_supplier')->toArray();
                   
                $validation = $event->sheet->getDataValidation('E2:E100000'); // Sesuaikan dengan rentang sel yang sesuai
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
           
                $validation->setFormula1('"' . implode(',', $supplier) . '"');
            }

        ];
    }
    
}
