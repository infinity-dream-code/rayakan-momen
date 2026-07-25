$files = @(
    "resources\views\katalog.blade.php",
    "resources\views\landing.blade.php",
    "resources\views\rsvp-bagikan.blade.php",
    "resources\views\rsvp-dashboard.blade.php",
    "resources\views\welcome.blade.php",
    "resources\views\admin\layout.blade.php",
    "resources\views\admin\login.blade.php",
    "resources\views\admin\pin.blade.php",
    "resources\views\templates\_layout.blade.php",
    "resources\views\undangan\expired.blade.php",
    "resources\views\undangan\show.blade.php",
    "resources\views\undangan\preview\couple.blade.php",
    "resources\views\undangan\preview\ultah.blade.php",
    "resources\views\undangan\preview\wedding.blade.php"
)

foreach ($file in $files) {
    if (-not (Test-Path $file)) {
        Write-Host "SKIP (tidak ditemukan): $file" -ForegroundColor Yellow
        continue
    }

    $content = Get-Content $file -Raw

    if ($content -match "partials\.favicon") {
        Write-Host "SKIP (sudah ada include): $file" -ForegroundColor Cyan
        continue
    }

    $newContent = $content -replace "(<head>)", "`$1`r`n    @include('partials.favicon')"

    if ($newContent -eq $content) {
        Write-Host "SKIP (tag <head> tidak ketemu): $file" -ForegroundColor Yellow
        continue
    }

    Set-Content -Path $file -Value $newContent -NoNewline
    Write-Host "OK: $file" -ForegroundColor Green
}

Write-Host "`nSelesai. Cek tiap file kalau ada yang SKIP." -ForegroundColor White