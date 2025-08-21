<?php

namespace App\Helpers;

use Mpdf\Mpdf;

class PdfHelper
{
    public static function createSimplePdf($html, $filename)
    {
        try {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'thsarabunnew',
                'fontDir' => [public_path('fonts')],
                'fontdata' => [
                    'thsarabunnew' => [
                        'R' => 'Sarabun-Regular.ttf',
                        'B' => 'Sarabun-Bold.ttf',
                    ]
                ]
            ]);
            
            $mpdf->WriteHTML($html);
            $mpdf->Output($filename, 'F');
            
            return file_exists($filename);
        } catch (Exception $e) {
            \Log::error('PDF Helper Error: ' . $e->getMessage());
            return false;
        }
    }
}