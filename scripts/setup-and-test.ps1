# Cleaned PowerShell helper script
# Setup and run tests for this Laravel + Vite project on Windows.
#
# This script does basic environment checks (PHP, Composer, Node/npm),
# installs PHP and JS dependencies, creates a .env from .env.example if needed,
# generates an application key, and runs the Laravel test suite (php artisan test).

param(
        [string]$PhpExe = 'php',
        [switch]$SkipInstall,
        [switch]$RunMigrations
)

param(
    [string]$PhpExe = 'php',
    [switch]$SkipInstall,
    [switch]$RunMigrations
)

function Write-Info($msg) { Write-Host ('[INFO]  {0}' -f $msg) -ForegroundColor Cyan }
function Write-Warn($msg) { Write-Host ('[WARN]  {0}' -f $msg) -ForegroundColor Yellow }
function Write-Err($msg)  { Write-Host ('[ERROR] {0}' -f $msg) -ForegroundColor Red }

Push-Location -Path (Split-Path -Path $MyInvocation.MyCommand.Definition -Parent) | Out-Null
Push-Location -Path '..' | Out-Null  # move to project root

$projectRoot = Get-Location
Write-Info "Project root: $projectRoot"

function Resolve-Php {
    try {
        & $PhpExe -v > $null 2>&1
        return $PhpExe
    } catch {
        # try common XAMPP path
        $xampp = 'C:\xampp\php\php.exe'
        if (Test-Path $xampp) {
            try { & $xampp -v > $null 2>&1; return $xampp } catch {}
        }
    }
    return $null
}

$php = Resolve-Php
if (-not $php) {
    Write-Err "No usable PHP executable found. Please install PHP or pass -PhpExe 'C:\xampp\php\php.exe'"
    Pop-Location; Pop-Location
    exit 2
}

Write-Info "Using PHP: $php"

function Check-Command($name, $checkCmd) {
    Write-Info "Checking $name..."
    try {
        & $checkCmd > $null 2>&1
        Write-Info ("{0} found" -f $name)
        return $true
    } catch {
        Write-Warn ("{0} not found or not available on PATH" -f $name)
        return $false
    }
}

$hasComposer = Check-Command 'Composer' 'composer --version'
$hasNode = Check-Command 'Node' 'node -v'
$hasNpm = Check-Command 'npm' 'npm -v'

if (-not $SkipInstall) {
    if (-not $hasComposer) {
        Write-Warn 'Composer not found. Trying to run via PHP + composer.phar if present.'
        if (Test-Path 'composer.phar') {
            Write-Info 'Running PHP composer.phar install...'
            & $php 'composer.phar' 'install'
        } else {
            Write-Err 'Composer not found and composer.phar missing. Please install Composer.'
            Pop-Location; Pop-Location
            exit 3
        }
    } else {
        Write-Info 'Running: composer install'
        composer install
    }

    if ($hasNpm) {
        Write-Info 'Running: npm install'
        npm install
    } elseif ($hasNode -and -not $hasNpm) {
        Write-Warn "Node is present but npm isn't. Skipping npm install."
    } else {
        Write-Warn 'Node/npm not available. Skipping JS dependency install.'
    }
} else {
    Write-Info 'Skipping dependency installation (SkipInstall set)'
}

# Ensure .env exists
if (-not (Test-Path '.env')) {
    if (Test-Path '.env.example') {
        Write-Info 'Creating .env from .env.example'
        Copy-Item .env.example .env
    } else {
        Write-Warn 'No .env or .env.example found. Tests may still run using phpunit.xml settings.'
    }
} else {
    Write-Info '.env already exists - leaving it alone'
}

Write-Info 'Generating application key (if missing)'
try {
    & $php 'artisan' 'key:generate' '--ansi'
} catch {
    Write-Warn 'artisan key:generate failed. Make sure application dependencies are installed.'
}

if ($RunMigrations) {
    Write-Info 'Running migrations (this changes your DB)'
    try { & $php 'artisan' 'migrate' '--force' } catch { Write-Err ("Migrations failed: {0}" -f $_) }
} else {
    Write-Info 'Skipping migrations (use -RunMigrations to enable)'
}

Write-Info 'Running test suite: php artisan test'
try {
    & $php 'artisan' 'test'
} catch {
    Write-Err ("Tests failed to run: {0}" -f $_)
    Pop-Location; Pop-Location
    exit 4
}

Pop-Location; Pop-Location
Write-Info 'Done.'
