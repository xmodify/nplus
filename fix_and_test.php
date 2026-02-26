<?php
$path = __DIR__ . '/app/Http/Controllers/Hnplus/ProductERController.php';
$content = file_get_contents($path);

// Replace double severe_type_null with single
$bad_str = "COALESCE(SUM(CASE WHEN et.export_code IS NULL OR et.export_code NOT IN ('1','2','3','4','5') THEN 1 ELSE 0 END), 0) AS severe_type_null,\n                COALESCE(SUM(CASE WHEN et.export_code IS NULL OR et.export_code NOT IN ('1','2','3','4','5') THEN 1 ELSE 0 END), 0) AS severe_type_null";
$good_str = "COALESCE(SUM(CASE WHEN et.export_code IS NULL OR et.export_code NOT IN ('1','2','3','4','5') THEN 1 ELSE 0 END), 0) AS severe_type_null";

$count = 0;
$content = str_replace($bad_str, $good_str, $content, $count);
$bad_str2 = "COALESCE(SUM(CASE WHEN et.export_code IS NULL OR et.export_code NOT IN ('1','2','3','4','5') THEN 1 ELSE 0 END), 0) AS severe_type_null,\r\n                COALESCE(SUM(CASE WHEN et.export_code IS NULL OR et.export_code NOT IN ('1','2','3','4','5') THEN 1 ELSE 0 END), 0) AS severe_type_null";
$content = str_replace($bad_str2, $good_str, $content, $count2);

file_put_contents($path, $content);
echo "Fixed $count or $count2 duplicates\n";

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$result = DB::connection('hosxp')->select("SELECT DATE(NOW()) AS vstdate, COALESCE(COUNT(DISTINCT e.vn), 0) AS visit, COALESCE(SUM(CASE WHEN et.export_code IN ('1') THEN 1 ELSE 0 END), 0) AS resuscitation, COALESCE(SUM(CASE WHEN et.export_code IN ('2') THEN 1 ELSE 0 END), 0) AS emergent, COALESCE(SUM(CASE WHEN et.export_code IN ('3') THEN 1 ELSE 0 END), 0) AS urgent, COALESCE(SUM(CASE WHEN et.export_code IN ('4') THEN 1 ELSE 0 END), 0) AS semi_urgent, COALESCE(SUM(CASE WHEN et.export_code IN ('5') THEN 1 ELSE 0 END), 0) AS non_urgent, COALESCE(SUM(CASE WHEN et.export_code IS NULL OR et.export_code NOT IN ('1','2','3','4','5') THEN 1 ELSE 0 END), 0) AS severe_type_null FROM er_regist e LEFT JOIN er_emergency_type et ON et.er_emergency_type = e.er_emergency_type WHERE DATE(e.enter_er_time) = CURDATE() AND TIME(e.enter_er_time) BETWEEN '08:00:00' AND '15:59:59'");

print_r($result);
