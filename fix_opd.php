<?php
$path = __DIR__ . '/app/Http/Controllers/Hnplus/ProductOPDController.php';
$content = file_get_contents($path);

// opd_morning_notify replacement
$search1 = <<<'EOD'
    public function opd_morning_notify()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');


        // Check format of settings, handle if null or empty. Assuming comma separated strings.
        // For SQL IN clause, if using simple variable interpolation:
        // '002','050' stored as value.
        // But code uses bindings usually or trusted values. Here we insert directly.

        $opd_dep = $opd_dep ?: "'002'";


        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");
EOD;

$replace1 = <<<'EOD'
    public function opd_morning_notify()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $ari_dep = MainSetting::where('name', 'ari_department')->value('value');
        $ari_dep = $ari_dep ?: "'036'";

        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END) AS opd,
            SUM(CASE WHEN main_dep IN ($ari_dep) THEN 1 ELSE 0 END) AS ari
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep) OR main_dep IN ($ari_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");
EOD;

// opd_morning replacement
$search2 = <<<'EOD'
    public function opd_morning()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');


        $opd_dep = $opd_dep ?: "'002'";


        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");

        return view('hnplus.product.opd_morning', compact('shift'));
    }
EOD;

$replace2 = <<<'EOD'
    public function opd_morning()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $ari_dep = MainSetting::where('name', 'ari_department')->value('value');
        $ari_dep = $ari_dep ?: "'036'";

        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END) AS opd,
            SUM(CASE WHEN main_dep IN ($ari_dep) THEN 1 ELSE 0 END) AS ari
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep) OR main_dep IN ($ari_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");

        return view('hnplus.product.opd_morning', compact('shift'));
    }
EOD;

// opd_bd_notify replacement
$search3 = <<<'EOD'
    public function opd_bd_notify()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');


        $opd_dep = $opd_dep ?: "'002'";


        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '16:00:00' AND '20:00:00' ");
EOD;

$replace3 = <<<'EOD'
    public function opd_bd_notify()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $ari_dep = MainSetting::where('name', 'ari_department')->value('value');
        $ari_dep = $ari_dep ?: "'036'";

        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END) AS opd,
            SUM(CASE WHEN main_dep IN ($ari_dep) THEN 1 ELSE 0 END) AS ari
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep) OR main_dep IN ($ari_dep))
            AND vsttime BETWEEN '16:00:00' AND '20:00:00' ");
EOD;

// opd_bd replacement
$search4 = <<<'EOD'
    public function opd_bd()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');


        $opd_dep = $opd_dep ?: "'002'";


        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '16:00:00' AND '20:00:00' ");

        return view('hnplus.product.opd_bd', compact('shift'));
    }
EOD;

$replace4 = <<<'EOD'
    public function opd_bd()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $ari_dep = MainSetting::where('name', 'ari_department')->value('value');
        $ari_dep = $ari_dep ?: "'036'";

        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END) AS opd,
            SUM(CASE WHEN main_dep IN ($ari_dep) THEN 1 ELSE 0 END) AS ari
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep) OR main_dep IN ($ari_dep))
            AND vsttime BETWEEN '16:00:00' AND '20:00:00' ");

        return view('hnplus.product.opd_bd', compact('shift'));
    }
EOD;

$content = str_replace($search1, $replace1, $content, $count1);
$content = str_replace($search2, $replace2, $content, $count2);
$content = str_replace($search3, $replace3, $content, $count3);
$content = str_replace($search4, $replace4, $content, $count4);

// Normalize newlines and replace generically if previous replacements failed due to exact newline matches
if ($count1 == 0 || $count2 == 0) {
    echo "Fallback to regex replacement...\n";

    // For morning
    $content = preg_replace(
        '/public function opd_morning_notify\(\)\s*\{[^{]*?(\$notify = DB::connection[^\}]+;)/s',
        "public function opd_morning_notify()\n    {\n        \$opd_dep = MainSetting::where('name', 'opd_department')->value('value');\n        \$opd_dep = \$opd_dep ?: \"'002'\";\n\n        \$ari_dep = MainSetting::where('name', 'ari_department')->value('value');\n        \$ari_dep = \$ari_dep ?: \"'036'\";\n\n        \$notify = DB::connection('hosxp')->select(\"\n            SELECT COUNT(DISTINCT vn) as patient_all,\n            SUM(CASE WHEN main_dep IN (\$opd_dep) THEN 1 ELSE 0 END) AS opd,\n            SUM(CASE WHEN main_dep IN (\$ari_dep) THEN 1 ELSE 0 END) AS ari\n            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN (\$opd_dep) OR main_dep IN (\$ari_dep))\n            AND vsttime BETWEEN '00:00:00' AND '15:59:59' \");",
        $content, 1, $count1
    );

    // For morning view
    $content = preg_replace(
        '/public function opd_morning\(\)\s*\{[^{]*?(\$shift = DB::connection[^\}]+?compact\(\'shift\'\)\);)/s',
        "public function opd_morning()\n    {\n        \$opd_dep = MainSetting::where('name', 'opd_department')->value('value');\n        \$opd_dep = \$opd_dep ?: \"'002'\";\n\n        \$ari_dep = MainSetting::where('name', 'ari_department')->value('value');\n        \$ari_dep = \$ari_dep ?: \"'036'\";\n\n        \$shift = DB::connection('hosxp')->select(\"\n            SELECT COUNT(DISTINCT vn) as patient_all,\n            SUM(CASE WHEN main_dep IN (\$opd_dep) THEN 1 ELSE 0 END) AS opd,\n            SUM(CASE WHEN main_dep IN (\$ari_dep) THEN 1 ELSE 0 END) AS ari\n            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN (\$opd_dep) OR main_dep IN (\$ari_dep))\n            AND vsttime BETWEEN '00:00:00' AND '15:59:59' \");\n\n        return view('hnplus.product.opd_morning', compact('shift'));",
        $content, 1, $count2
    );

    // For bd notify
    $content = preg_replace(
        '/public function opd_bd_notify\(\)\s*\{[^{]*?(\$notify = DB::connection[^\}]+;)/s',
        "public function opd_bd_notify()\n    {\n        \$opd_dep = MainSetting::where('name', 'opd_department')->value('value');\n        \$opd_dep = \$opd_dep ?: \"'002'\";\n\n        \$ari_dep = MainSetting::where('name', 'ari_department')->value('value');\n        \$ari_dep = \$ari_dep ?: \"'036'\";\n\n        \$notify = DB::connection('hosxp')->select(\"\n            SELECT COUNT(DISTINCT vn) as patient_all,\n            SUM(CASE WHEN main_dep IN (\$opd_dep) THEN 1 ELSE 0 END) AS opd,\n            SUM(CASE WHEN main_dep IN (\$ari_dep) THEN 1 ELSE 0 END) AS ari\n            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN (\$opd_dep) OR main_dep IN (\$ari_dep))\n            AND vsttime BETWEEN '16:00:00' AND '20:00:00' \");",
        $content, 1, $count3
    );

    // For bd view
    $content = preg_replace(
        '/public function opd_bd\(\)\s*\{[^{]*?(\$shift = DB::connection[^\}]+?compact\(\'shift\'\)\);)/s',
        "public function opd_bd()\n    {\n        \$opd_dep = MainSetting::where('name', 'opd_department')->value('value');\n        \$opd_dep = \$opd_dep ?: \"'002'\";\n\n        \$ari_dep = MainSetting::where('name', 'ari_department')->value('value');\n        \$ari_dep = \$ari_dep ?: \"'036'\";\n\n        \$shift = DB::connection('hosxp')->select(\"\n            SELECT COUNT(DISTINCT vn) as patient_all,\n            SUM(CASE WHEN main_dep IN (\$opd_dep) THEN 1 ELSE 0 END) AS opd,\n            SUM(CASE WHEN main_dep IN (\$ari_dep) THEN 1 ELSE 0 END) AS ari\n            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN (\$opd_dep) OR main_dep IN (\$ari_dep))\n            AND vsttime BETWEEN '16:00:00' AND '20:00:00' \");\n\n        return view('hnplus.product.opd_bd', compact('shift'));",
        $content, 1, $count4
    );
}

file_put_contents($path, $content);

echo "Replaced:\n1: $count1\n2: $count2\n3: $count3\n4: $count4\n";

// Validate
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$result = DB::connection('hosxp')->select("
    SELECT COUNT(DISTINCT vn) as patient_all,
    SUM(CASE WHEN main_dep IN ('002') THEN 1 ELSE 0 END) AS opd,
    SUM(CASE WHEN main_dep IN ('036') THEN 1 ELSE 0 END) AS ari
    FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ('002') OR main_dep IN ('036'))
    AND vsttime BETWEEN '00:00:00' AND '15:59:59' 
");
print_r($result);
