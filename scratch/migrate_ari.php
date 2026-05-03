<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$ari_data = DB::table('productivity_opd')->where('shift_time', 'LIKE', '%ARI%')->get();
foreach($ari_data as $row) {
    $data = (array)$row;
    unset($data['id']);
    DB::table('productivity_ari')->updateOrInsert(
        ['report_date' => $data['report_date'], 'shift_time' => $data['shift_time']],
        $data
    );
}
echo "Migrated " . count($ari_data) . " records.\n";
