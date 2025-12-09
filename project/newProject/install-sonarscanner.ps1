# Install SonarScanner for Windows

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "SonarScanner Installation" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$sonarScannerVersion = "5.0.1.3006"
$downloadUrl = "https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-$sonarScannerVersion-windows.zip"
$zipFile = "sonar-scanner.zip"
$installPath = "C:\sonar-scanner"

# Check if already installed
if (Test-Path "$installPath\bin\sonar-scanner.bat") {
    Write-Host "SonarScanner is already installed at: $installPath" -ForegroundColor Green
    Write-Host ""
    $reinstall = Read-Host "Do you want to reinstall? (y/N)"
    if ($reinstall -ne "y" -and $reinstall -ne "Y") {
        Write-Host "Installation cancelled." -ForegroundColor Yellow
        exit 0
    }
    Write-Host ""
    Write-Host "Removing existing installation..." -ForegroundColor Yellow
    Remove-Item -Path $installPath -Recurse -Force
}

# Download
Write-Host "Downloading SonarScanner $sonarScannerVersion..." -ForegroundColor Yellow
try {
    Invoke-WebRequest -Uri $downloadUrl -OutFile $zipFile -UseBasicParsing
    Write-Host "Download completed" -ForegroundColor Green
} catch {
    Write-Host "Download failed: $_" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Extract
Write-Host "Extracting to $installPath..." -ForegroundColor Yellow
try {
    Expand-Archive -Path $zipFile -DestinationPath "C:\" -Force
    
    # Rename the extracted folder
    $extractedFolder = "C:\sonar-scanner-$sonarScannerVersion-windows"
    if (Test-Path $extractedFolder) {
        Rename-Item -Path $extractedFolder -NewName "sonar-scanner"
    }
    
    Write-Host "Extraction completed" -ForegroundColor Green
} catch {
    Write-Host "Extraction failed: $_" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Cleanup
Write-Host "Cleaning up..." -ForegroundColor Yellow
Remove-Item -Path $zipFile -Force
Write-Host "Cleanup completed" -ForegroundColor Green

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Installation completed!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "SonarScanner installed at: $installPath" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Set up your SonarQube server (local or SonarCloud)" -ForegroundColor White
Write-Host "2. Generate a token from your SonarQube server" -ForegroundColor White
Write-Host "3. Run analysis script" -ForegroundColor White
Write-Host ""
