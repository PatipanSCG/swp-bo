<?php

namespace App\Services;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Illuminate\Support\Facades\View;

class MPdfService
{
    protected $mpdf;

    public function __construct()
    {
        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
            'default_font_size' => 14,
            'default_font' => 'thsarabunnew',
            'fontDir' => [public_path('fonts')],
            'fontdata' => [
                'thsarabunnew' => [
                    'R' => 'Sarabun-Regular.ttf',
                    'B' => 'Sarabun-Bold.ttf',
                    'I' => 'Sarabun-Italic.ttf',
                    'BI' => 'Sarabun-BoldItalic.ttf',
                ],
                'sarabun' => [
                    'R' => 'Sarabun-Regular.ttf',
                    'B' => 'Sarabun-Bold.ttf',
                    'I' => 'Sarabun-Italic.ttf',
                    'BI' => 'Sarabun-BoldItalic.ttf',
                ]
            ],
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);
    }

    public function loadView($view, $data = [])
    {
        $html = View::make($view, $data)->render();
        $this->mpdf->WriteHTML($html);
        return $this;
    }

    public function loadHTML($html)
    {
        $this->mpdf->WriteHTML($html);
        return $this;
    }

    public function save($filePath)
    {
        $this->mpdf->Output($filePath, Destination::FILE);
        return $filePath;
    }

    public function download($filename = 'document.pdf')
    {
        return $this->mpdf->Output($filename, Destination::DOWNLOAD);
    }

    public function stream($filename = 'document.pdf')
    {
        return $this->mpdf->Output($filename, Destination::INLINE);
    }

    public function output()
    {
        return $this->mpdf->Output('', Destination::STRING_RETURN);
    }

    public function getMpdf()
    {
        return $this->mpdf;
    }
}