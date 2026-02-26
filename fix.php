<?php
$path = __DIR__ . '/app/Http/Controllers/Hnplus/ProductERController.php';
$content = file_get_contents($path);

$search = "COALESCE(SUM(CASE WHEN et.export_code IN ('5') THEN 1 ELSE 0 END), 0) AS non_urgent";
$replace = "COALESCE(SUM(CASE WHEN et.export_code IN ('5') THEN 1 ELSE 0 END), 0) AS non_urgent,\n                COALESCE(SUM(CASE WHEN et.export_code IS NULL OR et.export_code NOT IN ('1','2','3','4','5') THEN 1 ELSE 0 END), 0) AS severe_type_null";

$count = 0;
$content = str_replace($search, $replace, $content, $count);

if ($count > 0) {
    file_put_contents($path, $content);
    echo "Replaced \$count occurrences in ProductERController.php\n";
}
else {
    echo "String not found in ProductERController.php\n";
}
