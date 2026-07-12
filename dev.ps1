param(
    [int]$port = 8000
)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  AbsensiMahasiswa - Dev Server Starter" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Kirim notifikasi server startup ke admin (via Telegram)
Write-Host "[1/3] Mengirim notifikasi startup..." -ForegroundColor Yellow
php artisan schedule:test-notification --startup
if ($LASTEXITCODE -eq 0) {
    Write-Host "  -> Notifikasi terkirim" -ForegroundColor Green
} else {
    Write-Host "  -> Notifikasi dilewati (token mungkin belum diisi)" -ForegroundColor DarkYellow
}
Write-Host ""

# 2. Jalankan scheduler di background (jendela terpisah, hidden)
Write-Host "[2/3] Menjalankan scheduler di background..." -ForegroundColor Yellow
$schedulerJob = Start-Process -WindowStyle Hidden -FilePath "powershell" -ArgumentList "-NoExit", "php artisan schedule:work" -PassThru
Write-Host "  -> Scheduler berjalan (PID: $($schedulerJob.Id))" -ForegroundColor Green
Write-Host ""

# 3. Jalankan serve di terminal utama
Write-Host "[3/3] Menjalankan server di http://localhost:$port ..." -ForegroundColor Yellow
Write-Host ""
Write-Host "  Tekan Ctrl+C untuk menghentikan server." -ForegroundColor DarkGray
Write-Host "  Scheduler akan ikut berhenti otomatis." -ForegroundColor DarkGray
Write-Host ""
php artisan serve --port=$port

# Bersihkan: stop scheduler saat serve berhenti
Write-Host ""
Write-Host "Membersihkan proses scheduler..." -ForegroundColor Yellow
Stop-Process -Id $schedulerJob.Id -Force -ErrorAction SilentlyContinue
Write-Host "Selesai." -ForegroundColor Green
