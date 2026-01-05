# Test API Laravel - Không có scan_time nữa
$apiUrl = "http://127.0.0.1:8000/api/scans"

$testData = @(
    @{
        STT = 1
        Barcode = "ABC123456"
        NgayGio = "2026-01-05 10:30:00"
        KetQua = "Đạt"
        Ca = "Sáng"
    },
    @{
        STT = 2
        Barcode = "DEF789012"
        NgayGio = "2026-01-05 14:15:00"
        KetQua = "Không đạt"
        Ca = "Chiều"
    }
)

$jsonBody = $testData | ConvertTo-Json -Depth 10

Write-Host "Đang gửi request đến: $apiUrl" -ForegroundColor Cyan
Write-Host "Dữ liệu gửi đi:" -ForegroundColor Yellow
Write-Host $jsonBody

try {
    $response = Invoke-RestMethod -Uri $apiUrl -Method Post -Body $jsonBody -ContentType "application/json"
    Write-Host "`nKết quả thành công!" -ForegroundColor Green
    Write-Host ($response | ConvertTo-Json)
} catch {
    Write-Host "`nLỗi khi gửi request:" -ForegroundColor Red
    Write-Host $_.Exception.Message
}
