# Run PHPUnit tests and list each test with Pass/Fail status

$phpPath = "c:\xampp\php\php.exe"
$phpunitPath = "phpunit.phar"
$testdoxFile = "testdox-output.txt"

Write-Host "Running tests..." -ForegroundColor Cyan
Write-Host ""

# First, run PHPUnit with --list-tests to get test names
Write-Host "Discovering tests..." -ForegroundColor Gray
$testList = & $phpPath -d xdebug.mode=off $phpunitPath --list-tests 2>&1 | Out-String
$testNames = @()
foreach ($line in ($testList -split "`r?`n")) {
    if ($line -match '^\s*-\s+(.+)$') {
        $testNames += $matches[1]
    }
}

Write-Host "Found $($testNames.Count) tests" -ForegroundColor Gray
Write-Host ""

# Run the actual tests
Write-Host "Executing tests..." -ForegroundColor Cyan
$output = & $phpPath -d xdebug.mode=off $phpunitPath 2>&1 | Out-String

# Parse the progress indicators (dots)
$progressLine = ""
foreach ($line in ($output -split "`r?`n")) {
    # Match lines that START with test progress symbols (dots, F, E, S, I, R)
    # This excludes lines like {"error"...} that might contain a dot
    # Also ignore single character lines (warnings from PHPUnit runner)
    if ($line -match '^([\.EFSIR]{2,})') {
        $progressLine += $matches[1]
    }
}

# Display each test with its result
Write-Host ""
Write-Host "Test Results:" -ForegroundColor Cyan
Write-Host ("=" * 80) -ForegroundColor Cyan

$index = 0
foreach ($testName in $testNames) {
    if ($index -lt $progressLine.Length) {
        $result = $progressLine[$index]
        switch ($result) {
            '.' {
                Write-Host "  [PASSED]  " -ForegroundColor Green -NoNewline
                Write-Host $testName
            }
            'F' {
                Write-Host "  [FAILED]  " -ForegroundColor Red -NoNewline
                Write-Host $testName
            }
            'E' {
                Write-Host "  [ERROR]   " -ForegroundColor Red -NoNewline
                Write-Host $testName
            }
            'S' {
                Write-Host "  [SKIPPED] " -ForegroundColor Yellow -NoNewline
                Write-Host $testName
            }
            'I' {
                Write-Host "  [INCOMPLETE] " -ForegroundColor Yellow -NoNewline
                Write-Host $testName
            }
            'R' {
                Write-Host "  [RISKY]   " -ForegroundColor Yellow -NoNewline
                Write-Host $testName
            }
        }
        $index++
    }
}

# Extract test statistics
$total = 0
$passed = 0
$failed = 0
$errors = 0
$skipped = 0
$incomplete = 0
$risky = 0

# Count results from progress line
if ($progressLine) {
    $total = $progressLine.Length
    $passed = ($progressLine.ToCharArray() | Where-Object { $_ -eq '.' }).Count
    $failed = ($progressLine.ToCharArray() | Where-Object { $_ -eq 'F' }).Count
    $errors = ($progressLine.ToCharArray() | Where-Object { $_ -eq 'E' }).Count
    $skipped = ($progressLine.ToCharArray() | Where-Object { $_ -eq 'S' }).Count
    $incomplete = ($progressLine.ToCharArray() | Where-Object { $_ -eq 'I' }).Count
    $risky = ($progressLine.ToCharArray() | Where-Object { $_ -eq 'R' }).Count
}

# Print summary
Write-Host ""
Write-Host ("=" * 80) -ForegroundColor Cyan
Write-Host "Test Summary:" -ForegroundColor Cyan
Write-Host ("=" * 80) -ForegroundColor Cyan
Write-Host "  Total Tests: $total"
Write-Host "  Passed:      " -NoNewline
Write-Host "$passed" -ForegroundColor Green
if ($failed -gt 0) {
    Write-Host "  Failed:      " -NoNewline
    Write-Host "$failed" -ForegroundColor Red
} else {
    Write-Host "  Failed:      $failed"
}
if ($errors -gt 0) {
    Write-Host "  Errors:      " -NoNewline
    Write-Host "$errors" -ForegroundColor Red
}
if ($skipped -gt 0) {
    Write-Host "  Skipped:     " -NoNewline
    Write-Host "$skipped" -ForegroundColor Yellow
}
if ($incomplete -gt 0) {
    Write-Host "  Incomplete:  " -NoNewline
    Write-Host "$incomplete" -ForegroundColor Yellow
}
if ($risky -gt 0) {
    Write-Host "  Risky:       " -NoNewline
    Write-Host "$risky" -ForegroundColor Yellow
}
Write-Host ("=" * 80) -ForegroundColor Cyan
Write-Host ""

# Display failure details if any
if ($failed -gt 0 -or $errors -gt 0) {
    Write-Host "Failure Details:" -ForegroundColor Red
    Write-Host $output
}

# Exit with proper code
if ($failed -gt 0 -or $errors -gt 0) {
    exit 1
} else {
    Write-Host "All tests passed!" -ForegroundColor Green
    exit 0
}
