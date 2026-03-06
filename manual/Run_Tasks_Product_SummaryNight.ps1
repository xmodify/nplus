# ============================================================
# [แก้ไขที่นี่] ระบุ URL หลักของระบบ (ไม่ต้องมี / ท้าย)
# ตัวอย่าง production: "http://192.168.1.100" หรือ "https://nplus.hospital.go.th"
# ============================================================
$baseUrl = "http://127.0.0.1:8000"

$paths = @(
    "/product/er_night_notify",
    "/product/ipd_night_notify",
    "/product/vip_night_notify",
    "/product/icu_night_notify",
    "/product/lr_night_notify"
)

$urls = $paths | ForEach-Object { "$baseUrl$_" }

# เริ่ม Jobs แบบ Parallel
$jobs = foreach ($url in $urls) {
    Start-Job -ScriptBlock {
        param($targetUrl)
        Invoke-WebRequest -Uri $targetUrl -UseBasicParsing -TimeoutSec 60
    } -ArgumentList $url
}

# รอให้ทุก Job เสร็จก่อน script ปิด (timeout 120 วินาที)
$jobs | Wait-Job -Timeout 120 | Out-Null
$jobs | Remove-Job -Force