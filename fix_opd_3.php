<?php
$path = __DIR__ . '/app/Http/Controllers/Hnplus/ProductOPDController.php';
$content = file_get_contents($path);

// Morning Notify
$s1 = <<<'EOD'
    public function opd_morning_notify()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $ari_dep = MainSetting::where('name', 'ari_department')->value('value');
        $ari_dep = $ari_dep ?: "'036'";

        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd,
            COALESCE(SUM(CASE WHEN main_dep IN ($ari_dep) THEN 1 ELSE 0 END), 0) AS ari
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep) OR main_dep IN ($ari_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");
EOD;

$r1 = <<<'EOD'
    public function opd_morning_notify()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");
EOD;

// Morning View
$s2 = <<<'EOD'
    public function opd_morning()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $ari_dep = MainSetting::where('name', 'ari_department')->value('value');
        $ari_dep = $ari_dep ?: "'036'";

        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd,
            COALESCE(SUM(CASE WHEN main_dep IN ($ari_dep) THEN 1 ELSE 0 END), 0) AS ari
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep) OR main_dep IN ($ari_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");
EOD;

$r2 = <<<'EOD'
    public function opd_morning()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");
EOD;

// BD Notify
$s3 = <<<'EOD'
    public function opd_bd_notify()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $ari_dep = MainSetting::where('name', 'ari_department')->value('value');
        $ari_dep = $ari_dep ?: "'036'";

        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd,
            COALESCE(SUM(CASE WHEN main_dep IN ($ari_dep) THEN 1 ELSE 0 END), 0) AS ari
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep) OR main_dep IN ($ari_dep))
            AND vsttime BETWEEN '16:00:00' AND '20:00:00' ");
EOD;

$r3 = <<<'EOD'
    public function opd_bd_notify()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '16:00:00' AND '20:00:00' ");
EOD;

// BD View
$s4 = <<<'EOD'
    public function opd_bd()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $ari_dep = MainSetting::where('name', 'ari_department')->value('value');
        $ari_dep = $ari_dep ?: "'036'";

        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd,
            COALESCE(SUM(CASE WHEN main_dep IN ($ari_dep) THEN 1 ELSE 0 END), 0) AS ari
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep) OR main_dep IN ($ari_dep))
            AND vsttime BETWEEN '16:00:00' AND '20:00:00' ");
EOD;

$r4 = <<<'EOD'
    public function opd_bd()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '16:00:00' AND '20:00:00' ");
EOD;

$content = str_replace($s1, $r1, $content, $c1);
$content = str_replace($s2, $r2, $content, $c2);
$content = str_replace($s3, $r3, $content, $c3);
$content = str_replace($s4, $r4, $content, $c4);

file_put_contents($path, $content);
echo "Replaced:\n1: $c1\n2: $c2\n3: $c3\n4: $c4\n";

// Validate
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$result = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ('002','050') THEN 1 ELSE 0 END), 0) AS opd
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ('002','050'))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' 
");
print_r($result);
