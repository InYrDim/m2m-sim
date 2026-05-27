# PowerShell script to build Docker image for M2M-SIM application
# Usage: .\scripts\build-docker.ps1 [tag]

# Configuration
$ImageName = "iyedeh/m2m-sim-app"
$DefaultTag = "latest"

# Use provided tag or default
if ($args.Count -gt 0) {
    $Tag = $args[0]
} else {
    $Tag = $DefaultTag
}
$FullImageName = "${ImageName}:${Tag}"

Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "    M2M-SIM Docker Build Script" -ForegroundColor Cyan
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "Building image: $FullImageName" -ForegroundColor Yellow
Write-Host ""

# Check if Docker is running
try {
    docker info > $null 2>$null
} catch {
    Write-Host "Error: Docker is not running or not installed." -ForegroundColor Red
    Exit 1
}

# Build the Docker image
Write-Host "Building Docker image..." -ForegroundColor Green
docker build -t $FullImageName .

# Optional: Also tag as latest if not already
if ($Tag -ne "latest") {
    Write-Host "Tagging as latest..." -ForegroundColor Green
    docker tag $FullImageName "${ImageName}:latest"
}

Write-Host ""
Write-Host "===============================================" -ForegroundColor Green
Write-Host "    SUCCESS: Docker image built successfully!" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Green
Write-Host "Image: $FullImageName" -ForegroundColor Yellow
Write-Host ""
Write-Host "To push to Docker Hub:" -ForegroundColor Yellow
Write-Host "  docker push $FullImageName" -ForegroundColor Yellow
if ($Tag -ne "latest") {
    Write-Host "  docker push ${ImageName}:latest" -ForegroundColor Yellow
}
Write-Host ""
Write-Host "To run locally:" -ForegroundColor Yellow
Write-Host "  docker compose up -d" -ForegroundColor Yellow
Write-Host "===============================================" -ForegroundColor Green