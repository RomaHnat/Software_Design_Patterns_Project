# Run SonarQube Analysis

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "SonarQube Analysis Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Generate coverage reports
Write-Host "Step 1: Running tests and generating coverage reports..." -ForegroundColor Yellow
$phpPath = "c:\xampp\php\php.exe"
$phpunitPath = "phpunit.phar"

& $phpPath $phpunitPath --coverage-clover coverage/clover.xml --log-junit coverage/phpunit-report.xml

if ($LASTEXITCODE -eq 0) {
    Write-Host "Coverage reports generated successfully" -ForegroundColor Green
} else {
    Write-Host "Failed to generate coverage reports" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Step 2: Check if SonarScanner is installed
Write-Host "Step 2: Checking SonarScanner installation..." -ForegroundColor Yellow

$sonarScannerPath = "C:\sonar-scanner\bin\sonar-scanner.bat"

if (-not (Test-Path $sonarScannerPath)) {
    Write-Host "SonarScanner not found at: $sonarScannerPath" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please install SonarScanner first:" -ForegroundColor Yellow
    Write-Host "1. Download from: https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-5.0.1.3006-windows.zip" -ForegroundColor White
    Write-Host "2. Extract to: C:\sonar-scanner" -ForegroundColor White
    Write-Host "3. Or run the installation script: install-sonarscanner.ps1" -ForegroundColor White
    Write-Host ""
    exit 1
}

Write-Host "SonarScanner found" -ForegroundColor Green
Write-Host ""

# Step 3: Check for SonarQube server configuration
Write-Host "Step 3: SonarQube Configuration..." -ForegroundColor Yellow

$sonarToken = $env:SONAR_TOKEN
$sonarHost = $env:SONAR_HOST_URL

if (-not $sonarToken) {
    Write-Host "SONAR_TOKEN environment variable not set" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Choose your SonarQube setup:" -ForegroundColor Cyan
    Write-Host "1. Local SonarQube Server (http://localhost:9000)" -ForegroundColor White
    Write-Host "2. SonarCloud (https://sonarcloud.io)" -ForegroundColor White
    Write-Host "3. Custom SonarQube Server" -ForegroundColor White
    Write-Host ""
    
    $choice = Read-Host "Enter your choice (1-3)"
    
    switch ($choice) {
        "1" {
            $sonarHost = "http://localhost:9000"
            Write-Host ""
            Write-Host "Using local SonarQube server: $sonarHost" -ForegroundColor Green
            Write-Host "Default login: admin / admin" -ForegroundColor Gray
            Write-Host ""
            $sonarToken = Read-Host "Enter your SonarQube token (generate at: $sonarHost/account/security)"
        }
        "2" {
            $sonarHost = "https://sonarcloud.io"
            Write-Host ""
            Write-Host "Using SonarCloud: $sonarHost" -ForegroundColor Green
            Write-Host ""
            $sonarToken = Read-Host "Enter your SonarCloud token (generate at: https://sonarcloud.io/account/security)"
            $sonarOrg = Read-Host "Enter your SonarCloud organization key"
        }
        "3" {
            $sonarHost = Read-Host "Enter SonarQube server URL (e.g., https://sonar.yourcompany.com)"
            $sonarToken = Read-Host "Enter your SonarQube token"
        }
        default {
            Write-Host "Invalid choice" -ForegroundColor Red
            exit 1
        }
    }
}

if (-not $sonarHost) {
    $sonarHost = "http://localhost:9000"
}

Write-Host ""
Write-Host "Configuration:" -ForegroundColor Cyan
Write-Host "  Host: $sonarHost" -ForegroundColor White
Write-Host "  Token: $($sonarToken.Substring(0, [Math]::Min(10, $sonarToken.Length)))..." -ForegroundColor White
Write-Host ""

# Step 4: Run SonarScanner
Write-Host "Step 4: Running SonarScanner analysis..." -ForegroundColor Yellow
Write-Host ""

$args = @(
    "-Dsonar.host.url=$sonarHost"
    "-Dsonar.login=$sonarToken"
)

if ($sonarOrg) {
    $args += "-Dsonar.organization=$sonarOrg"
}

& $sonarScannerPath $args

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "SonarQube analysis completed!" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "View results at: $sonarHost/dashboard?id=water-management-system" -ForegroundColor Cyan
} else {
    Write-Host ""
    Write-Host "SonarQube analysis failed" -ForegroundColor Red
    exit 1
}
