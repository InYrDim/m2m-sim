# PowerShell Script to run M2M-SIM in Docker on Windows

Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "    M2M-SIM Docker Orchestration Starter       " -ForegroundColor Cyan
Write-Host "===============================================" -ForegroundColor Cyan

# Check if Docker is running
& docker ps > $null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error: Docker Desktop is not running or not installed." -ForegroundColor Red
    Exit
}

# Check if .env file exists
if (-not (Test-Path .env)) {
    Write-Host "[!] .env file not found. Copying .env.example..." -ForegroundColor Yellow
    Copy-Item .env.example .env
    
    # Configure .env to use the docker db service by default
    (Get-Content .env) -replace 'DB_HOST=127.0.0.1', 'DB_HOST=db' `
                       -replace 'DB_USERNAME=root', 'DB_USERNAME=laravel_user' `
                       -replace 'DB_PASSWORD=aLP1aylX4IgTdwoXEw1MX669ot7VQgTa', 'DB_PASSWORD=laravel_password' | Set-Content .env
}

Write-Host "[1/3] Building and starting Docker containers..." -ForegroundColor Green
docker compose up -d --build

Write-Host "[2/3] Waiting for the database to be ready..." -ForegroundColor Green
# Wait for MariaDB to respond
do {
    Start-Sleep -Seconds 2
    $ping = docker compose exec db mysqladmin ping -h"localhost" -u"laravel_user" -p"laravel_password" --silent
} while ($LASTEXITCODE -ne 0)

Write-Host "[3/3] Setting up Laravel application inside container..." -ForegroundColor Green

# Generate key if needed
$envContent = Get-Content .env
if ($envContent -match "APP_KEY=$" -or $envContent -match "APP_KEY=base64") {
    Write-Host "Generating application key..." -ForegroundColor Blue
    docker compose exec app php artisan key:generate
}

# Set correct storage permissions
Write-Host "Setting directory permissions..." -ForegroundColor Blue
docker compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run migrations and seeders
Write-Host "Running database migrations and seeding..." -ForegroundColor Blue
docker compose exec app php artisan migrate --seed --force

# Create storage link
Write-Host "Creating storage symbolic link..." -ForegroundColor Blue
docker compose exec app php artisan storage:link

Write-Host "===============================================" -ForegroundColor Green
Write-Host "    SUCCESS: M2M-SIM is up and running!        " -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Green
Write-Host "Web App:      http://localhost:8000" -ForegroundColor Yellow
Write-Host "Filament:     http://localhost:8000/admin" -ForegroundColor Yellow
Write-Host "Database Port:33061" -ForegroundColor Yellow
Write-Host "Stop Services: docker compose down" -ForegroundColor Yellow
Write-Host "===============================================" -ForegroundColor Green
