<?php

$assets = [
    // jQuery
    'public/vendor/jquery/jquery-3.7.1.min.js' => 'https://code.jquery.com/jquery-3.7.1.min.js',
    'public/vendor/jquery/jquery-3.5.1.js' => 'https://code.jquery.com/jquery-3.5.1.js',

    // Bootstrap
    'public/vendor/bootstrap/css/bootstrap.min.css' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
    'public/vendor/bootstrap/js/bootstrap.bundle.min.js' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
    'public/vendor/bootstrap/css/bootstrap-5.3.3.min.css' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',

    // Bootstrap Icons
    'public/vendor/bootstrap-icons/bootstrap-icons.css' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
    'public/vendor/bootstrap-icons/bootstrap-icons.min.css' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'public/vendor/bootstrap-icons/fonts/bootstrap-icons.woff2' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2',
    'public/vendor/bootstrap-icons/fonts/bootstrap-icons.woff' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff',

    // Font Awesome
    'public/vendor/font-awesome/css/all.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    'public/vendor/font-awesome/webfonts/fa-solid-900.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-solid-900.woff2',
    'public/vendor/font-awesome/webfonts/fa-solid-900.ttf' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-solid-900.ttf',
    'public/vendor/font-awesome/webfonts/fa-regular-400.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-regular-400.woff2',
    'public/vendor/font-awesome/webfonts/fa-regular-400.ttf' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-regular-400.ttf',
    'public/vendor/font-awesome/webfonts/fa-brands-400.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-brands-400.woff2',
    'public/vendor/font-awesome/webfonts/fa-brands-400.ttf' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-brands-400.ttf',
    'public/vendor/font-awesome/webfonts/fa-v4compat.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-v4compat.woff2',
    'public/vendor/font-awesome/webfonts/fa-v4compat.ttf' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-v4compat.ttf',

    // SweetAlert2
    'public/vendor/sweetalert2/sweetalert2.all.min.js' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
    'public/vendor/sweetalert2/sweetalert2.min.js' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js',
    'public/vendor/sweetalert2/sweetalert2.min.css' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',

    // Bootstrap Datepicker
    'public/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css',
    'public/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js',
    'public/vendor/bootstrap-datepicker/locales/bootstrap-datepicker.th.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js',

    // DataTables
    'public/vendor/datatables/jquery.dataTables.min.js' => 'https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js',
    'public/vendor/datatables/dataTables.bootstrap5.min.js' => 'https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js',
    'public/vendor/datatables/dataTables.buttons.min.js' => 'https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js',
    'public/vendor/datatables/buttons.html5.min.js' => 'https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js',

    // JSZip
    'public/vendor/jszip/jszip.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',

    // Chart.js & datalabels
    'public/vendor/chartjs/chart.js' => 'https://cdn.jsdelivr.net/npm/chart.js',
    'public/vendor/chartjs/chartjs-plugin-datalabels.js' => 'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels',
    'public/vendor/chartjs/chartjs-plugin-datalabels.min.js' => 'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js',
];

$baseDir = dirname(__DIR__) . '/';

foreach ($assets as $localPath => $url) {
    $fullPath = $baseDir . $localPath;
    $dir = dirname($fullPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo "Created directory: $dir\n";
    }

    echo "Downloading $url to $localPath...\n";
    
    // Set custom user agent to avoid blockage
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $content = file_get_contents($url, false, $context);
    
    if ($content === false) {
        echo "❌ Failed to download $url\n";
    } else {
        file_put_contents($fullPath, $content);
        echo "✅ Downloaded successfully\n";
    }
}

echo "All downloads completed!\n";
