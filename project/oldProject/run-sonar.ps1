# SonarQube Analysis Script for Old Code
Write-Host "========================================"
Write-Host "SonarQube Analysis for Old Code"
Write-Host "========================================"
Write-Host ""

# Check if SonarScanner is installed
Write-Host "Checking SonarScanner installation..."
$sonarScannerPath = "C:\sonar-scanner\bin\sonar-scanner.bat"

if (-not (Test-Path $sonarScannerPath)) {
    Write-Host "ERROR: SonarScanner not found at $sonarScannerPath" -ForegroundColor Red
    Write-Host "Please run the install-sonarscanner.ps1 script first" -ForegroundColor Yellow
    exit 1
}

Write-Host "SonarScanner found" -ForegroundColor Green
Write-Host ""

# Check for SONAR_TOKEN environment variable
Write-Host "SonarQube Configuration..."
if (-not $env:SONAR_TOKEN) {
    Write-Host "SONAR_TOKEN environment variable not set" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Choose your SonarQube setup:"
    Write-Host "1. Local SonarQube Server (http://localhost:9000)"
    Write-Host "2. SonarCloud (https://sonarcloud.io)"
    Write-Host "3. Custom SonarQube Server"
    Write-Host ""
    
    $choice = Read-Host "Enter your choice (1-3)"
    
    switch ($choice) {
        "1" {
            Write-Host ""
            Write-Host "Using Local SonarQube Server" -ForegroundColor Cyan
            $token = Read-Host "Enter your SonarQube token"
            $env:SONAR_HOST_URL = "http://localhost:9000"
            $env:SONAR_TOKEN = $token
        }
        "2" {
            Write-Host ""
            Write-Host "Using SonarCloud" -ForegroundColor Cyan
            $token = Read-Host "Enter your SonarCloud token"
            $organization = Read-Host "Enter your SonarCloud organization key"
            $env:SONAR_HOST_URL = "https://sonarcloud.io"
            $env:SONAR_TOKEN = $token
            $env:SONAR_ORGANIZATION = $organization
        }
        "3" {
            Write-Host ""
            Write-Host "Using Custom SonarQube Server" -ForegroundColor Cyan
            $hostUrl = Read-Host "Enter your SonarQube server URL"
            $token = Read-Host "Enter your SonarQube token"
            $env:SONAR_HOST_URL = $hostUrl
            $env:SONAR_TOKEN = $token
        }
        default {
            Write-Host "Invalid choice" -ForegroundColor Red
            exit 1
        }
    }
}

# Run SonarScanner
Write-Host ""
Write-Host "Running SonarScanner analysis..." -ForegroundColor Cyan
Write-Host ""

$sonarArgs = @(
    "-Dsonar.host.url=$($env:SONAR_HOST_URL)",
    "-Dsonar.token=$($env:SONAR_TOKEN)"
)

if ($env:SONAR_ORGANIZATION) {
    $sonarArgs += "-Dsonar.organization=$($env:SONAR_ORGANIZATION)"
}

& $sonarScannerPath $sonarArgs

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "========================================"
    Write-Host "SonarQube Analysis Completed Successfully!" -ForegroundColor Green
    Write-Host "========================================"
    Write-Host ""
    Write-Host "View your results at:" -ForegroundColor Cyan
    if ($env:SONAR_ORGANIZATION) {
        Write-Host "$($env:SONAR_HOST_URL)/dashboard?id=water-management-system-old-code"
    } else {
        Write-Host "$($env:SONAR_HOST_URL)/dashboard?id=water-management-system-old-code"
    }
} else {
    Write-Host ""
    Write-Host "========================================"
    Write-Host "SonarQube Analysis Failed" -ForegroundColor Red
    Write-Host "========================================"
    exit 1
}
