<?php
$path = __DIR__ . '/app/Http/Controllers/Hnplus/ProductOPDController.php';
$content = file_get_contents($path);

// Replace raw SUM(...) with COALESCE(SUM(...), 0)
$content = str_replace(
    "SUM(CASE WHEN main_dep IN (\$opd_dep) THEN 1 ELSE 0 END) AS opd",
    "COALESCE(SUM(CASE WHEN main_dep IN (\$opd_dep) THEN 1 ELSE 0 END), 0) AS opd",
    $content
);
$content = str_replace(
    "SUM(CASE WHEN main_dep IN (\$ari_dep) THEN 1 ELSE 0 END) AS ari",
    "COALESCE(SUM(CASE WHEN main_dep IN (\$ari_dep) THEN 1 ELSE 0 END), 0) AS ari",
    $content
);

file_put_contents($path, $content);

// Validate
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$result = DB::connection('hosxp')->select("
    SELECT COUNT(DISTINCT vn) as patient_all,
    COALESCE(SUM(CASE WHEN main_dep IN ('002') THEN 1 ELSE 0 END), 0) AS opd,
    COALESCE(SUM(CASE WHEN main_dep IN ('036') THEN 1 ELSE 0 END), 0) AS ari
    FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ('002') OR main_dep IN ('036'))
    AND vsttime BETWEEN '00:00:00' AND '15:59:59' 
");
print_r($result);
