<?php

// สร้างไฟล์ config/dompdf.php
return [
    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Set some default values. It is possible to add all defines that can be set
    | in dompdf_config.inc.php. You can also override the entire config file.
    |
    */
    'show_warnings' => false,
    'public_path' => null,
    'convert_entities' => true,
    'options' => [
        /*
         * The location of the DOMPDF font directory
         *
         * The location of the directory where DOMPDF will store fonts and font metrics
         * Note: This directory must exist and be writable by the webserver process.
         * *Please note the trailing slash.*
         *
         * Notes regarding fonts:
         * Additional .afm font metrics can be added by executing load_font.php from command line.
         *
         * Only the original "Base 14 fonts" are present on all pdf viewers. Additional fonts must
         * be embedded in the pdf file or the PDF may not display correctly. This can significantly
         * increase file size unless font subsetting is enabled. Before embedding a font please
         * review your rights under the font license.
         *
         * Any font specification in the source HTML is translated to the closest font available
         * in the font directory.
         *
         * The pdf standard "Base 14 fonts" are:
         * Courier, Courier-Bold, Courier-Oblique, Courier-BoldOblique,
         * Helvetica, Helvetica-Bold, Helvetica-Oblique, Helvetica-BoldOblique,
         * Times-Roman, Times-Bold, Times-Italic, Times-BoldItalic,
         * Symbol, ZapfDingbats.
         */
        'font_dir' => storage_path('fonts'),

        /*
         * The location of the DOMPDF font cache directory
         *
         * This directory contains the cached font metrics for the fonts used by DOMPDF.
         * This directory can be the same as DOMPDF_FONT_DIR
         *
         * Note: This directory must exist and be writable by the webserver process.
         */
        'font_cache' => storage_path('fonts'),

        /*
         * The location of a temporary directory.
         *
         * The directory specified must be writeable by the webserver process.
         * The temporary directory is required to download remote images and when
         * using the PFDLib back end.
         */
        'temp_dir' => sys_get_temp_dir(),

        /*
         * ==== IMPORTANT ====
         *
         * dompdf's "chroot": Prevents dompdf from accessing system files or other
         * files on the webserver.  All local files opened by dompdf must be in a
         * subdirectory of this directory.  DO NOT set it to '/' since this could
         * allow an attacker to use dompdf to read any file on the server.  This
         * should be an absolute path.
         * This is only checked on command line call by dompdf.php, but not by
         * direct class use like:
         * $dompdf = new DOMPDF();	$dompdf->load_html($htmldata); $dompdf->render(); $pdfdata = $dompdf->output();
         */
        'chroot' => realpath(base_path()),

        /*
         * Protocol whitelist
         *
         * Protocols and PHP wrappers allowed in URIs, and the validation rules
         * that determine if a resouce may be loaded. Full support is not guaranteed
         * for the protocols/wrappers specified
         * by this array.
         *
         * @var array
         */
        'allowed_protocols' => [
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []]
        ],

        /*
         * @var string
         */
        'log_output_file' => storage_path('logs/dompdf.log'),

        /*
         * Enable inline PHP
         *
         * If this setting is set to true then DOMPDF will automatically evaluate
         * inline PHP contained within <script type="text/php"> ... </script> tags.
         *
         * Enabling this for documents you do not trust (e.g. arbitrary remote html
         * pages) is a security risk.  Set this option to false if you wish to process
         * untrusted documents.
         */
        'enable_php' => false,

        /*
         * Enable inline Javascript
         *
         * If this setting is set to true then DOMPDF will automatically insert
         * JavaScript code contained within <script type="text/javascript"> ... </script> tags.
         */
        'enable_javascript' => true,

        /*
         * Enable remote file access
         *
         * If this setting is set to true, DOMPDF will access remote sites for
         * images and CSS files as required.
         * This is required for part of test case www/test/image_variants.html through www/examples.php
         *
         * Attention!
         * This can be a security risk, in particular in combination with DOMPDF_ENABLE_PHP and
         * allowing remote access to dompdf.php or on allowing remote html code to be passed to
         * $dompdf = new DOMPDF(); $dompdf->load_html($htmldata); $dompdf->render(); $pdfdata = $dompdf->output();
         * This allows anonymous users to download legally doubtful internet content which on
         * tracing back appears to being downloaded by your server, or allows malicious php code
         * in remote html pages to be executed by your server with your account privileges.
         */
        'enable_remote' => true,

        /*
         * A ratio applied to the fonts height to be more like browsers' line height
         */
        'font_height_ratio' => 1.1,

        /*
         * Use the HTML5 Lib parser
         *
         * Recommended, but relies on the presence of the HTML5 Lib
         *
         * @var bool
         */
        'enable_html5_parser' => true,
    ],
];

// หลังจากสร้างไฟล์แล้ว ต้อง publish config
// php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"