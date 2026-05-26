<?php

$dir = dirname(__DIR__) . '/resources/views';

$replacements = [
    // SweetAlert2
    'https://cdn.jsdelivr.net/npm/sweetalert2@11' => "{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}",

    // Bootstrap Icons
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css' => "{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}",
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css' => "{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}",

    // DataTables scripts
    'https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js' => "{{ asset('vendor/datatables/jquery.dataTables.min.js') }}",
    'https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js' => "{{ asset('vendor/datatables/dataTables.bootstrap5.min.js') }}",
    'https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js' => "{{ asset('vendor/datatables/dataTables.buttons.min.js') }}",
    'https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js' => "{{ asset('vendor/datatables/buttons.html5.min.js') }}",

    // JSZip
    'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js' => "{{ asset('vendor/jszip/jszip.min.js') }}",

    // Chart.js
    'https://cdn.jsdelivr.net/npm/chart.js' => "{{ asset('vendor/chartjs/chart.js') }}",
    'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels' => "{{ asset('vendor/chartjs/chartjs-plugin-datalabels.min.js') }}",
];

// Recursive directory iterator
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getRealPath();
        $content = file_get_contents($filePath);
        $originalContent = $content;

        // Perform standard string replacements
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        // Special handling for Bootstrap 5.3.3 CSS tag with integrity/crossorigin attributes
        // regex to match: <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"[^>]*>
        $pattern = '/<link\s+href="https:\/\/cdn\.jsdelivr\.net\/npm\/bootstrap@5\.3\.3\/dist\/css\/bootstrap\.min\.css"[^>]*>/i';
        $content = preg_replace($pattern, '<link href="{{ asset(\'vendor/bootstrap/css/bootstrap-5.3.3.min.css\') }}" rel="stylesheet">', $content);

        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            echo "Refactored: " . str_replace(dirname(__DIR__), '', $filePath) . "\n";
        }
    }
}

echo "View refactoring complete!\n";
