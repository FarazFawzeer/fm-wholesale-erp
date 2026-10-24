<?php

/**
 * Build an update ("patch") zip with only the files changed since the last upload to live.
 *
 *   deploy-patch.bat init   mark the current commit as what is live now (first time only)
 *   deploy-patch.bat        build deploy/patch-<date>.zip + upload steps
 *   deploy-patch.bat done   after uploading: mark the current commit as live
 */

const TAG_PREFIX = 'deployed-';

// Never sent to live: secrets, live data, sources that are compiled, dev-only files
const SKIP = [
    '.env', 'storage/', 'public/uploads/', 'public/build/', 'public/hot', 'node_modules/', 'vendor/', 'tests/', 'docs/', 'deploy/', 'tools/',
    'resources/js/', 'resources/scss/', 'resources/fonts/', 'package.json', 'package-lock.json', 'yarn.lock', 'bun.lockb',
    'vite.config.js', 'phpunit.xml', 'README.md', 'DEPLOY.md', '.gitignore', '.gitattributes', '.editorconfig', 'deploy-patch.bat',
    'bootstrap/cache/',
];
// Changing any of these means the CSS/JS must be rebuilt
const BUILD_SOURCES = ['resources/js/', 'resources/scss/', 'resources/fonts/', 'vite.config.js', 'package.json', 'package-lock.json'];

chdir(dirname(__DIR__));
date_default_timezone_set('Asia/Colombo');
if (function_exists('sapi_windows_vt100_support')) {
    @sapi_windows_vt100_support(STDOUT, true); // colours in cmd.exe
}
$mode = $argv[1] ?? 'build';

function git(string $args): string
{
    return trim((string) shell_exec('git ' . $args . ' 2>&1'));
}

function line(string $text = '', string $color = ''): void
{
    $codes = ['green' => "\033[32m", 'red' => "\033[31m", 'yellow' => "\033[33m", 'cyan' => "\033[36m"];
    echo ($codes[$color] ?? '') . $text . ($color ? "\033[0m" : '') . PHP_EOL;
}

function startsWithAny(string $path, array $prefixes): bool
{
    foreach ($prefixes as $p) {
        if ($path === rtrim($p, '/') || str_starts_with($path, $p)) {
            return true;
        }
    }

    return false;
}

function stopIfUncommitted(): void
{
    $dirty = git('status --porcelain');
    if ($dirty !== '') {
        line('You have changes that are not committed yet:', 'red');
        foreach (array_slice(explode("\n", $dirty), 0, 15) as $l) {
            line('   ' . $l);
        }
        line();
        line('Commit them first (VS Code Source Control, or: git add -A  then  git commit -m "message")', 'yellow');
        line('then run deploy-patch.bat again.', 'yellow');
        exit(1);
    }
}

function lastTag(): ?string
{
    $tags = array_filter(explode("\n", git('tag --list "' . TAG_PREFIX . '*" --sort=-creatordate')));

    return $tags ? array_values($tags)[0] : null;
}

function tagNow(): string
{
    $tag = TAG_PREFIX . date('Y-m-d-His');
    git('tag ' . $tag);

    return $tag;
}

if (trim(git('rev-parse --is-inside-work-tree')) !== 'true') {
    line('This folder is not a git repository.', 'red');
    exit(1);
}

// ---------- init / done: remember what is live ----------
if ($mode === 'init' || $mode === 'done') {
    stopIfUncommitted();
    $tag = tagNow();
    line($mode === 'init'
        ? "Baseline saved ($tag). From now on deploy-patch.bat packs only what changes after this point."
        : "Marked as live ($tag). The next patch will start from here.", 'green');
    exit(0);
}

// ---------- build the patch ----------
$base = lastTag();
if (! $base) {
    line('No baseline yet. Run this once first:  deploy-patch.bat init', 'yellow');
    line('(do it when your live site matches your latest commit)');
    exit(1);
}
stopIfUncommitted();

$changes = array_filter(explode("\n", git("diff --name-status -M $base HEAD")));
if (! $changes) {
    line("Nothing changed since the last upload ($base).", 'green');
    exit(0);
}

$upload = [];
$deleted = [];
$needsBuild = false;
$migrations = [];
$composer = false;

foreach ($changes as $row) {
    $parts = preg_split('/\t/', $row);
    $status = $parts[0][0];
    $path = end($parts);
    if ($status === 'R') {
        $old = $parts[1];
        if (! startsWithAny($old, SKIP)) {
            $deleted[] = $old;
        }
    }
    if (startsWithAny($path, BUILD_SOURCES)) {
        $needsBuild = true;
    }
    if ($path === 'composer.lock') {
        $composer = true;
    }
    if ($status === 'D') {
        if (! startsWithAny($path, SKIP)) {
            $deleted[] = $path;
        }
        continue;
    }
    if (str_starts_with($path, 'database/migrations/') && $status !== 'D') {
        $migrations[] = basename($path);
    }
    if (! startsWithAny($path, SKIP)) {
        $upload[] = $path;
    }
}

if ($needsBuild) {
    line('Design/script files changed - building CSS/JS (npm run build)...', 'cyan');
    exec('npm run build 2>&1', $buildOutput, $code);
    if ($code !== 0) {
        echo implode(PHP_EOL, $buildOutput) . PHP_EOL;
        line('The build failed. Fix the error above and try again.', 'red');
        exit(1);
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('public/build', FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        $upload[] = str_replace('\\', '/', $f->getPathname());
    }
}

$upload = array_values(array_unique($upload));
if (! $upload && ! $deleted) {
    line("Only development files changed since $base - nothing to upload.", 'green');
    exit(0);
}

@mkdir('deploy', 0777, true);
$stamp = date('Y-m-d-Hi');
$zipPath = "deploy/patch-$stamp.zip";
$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    line("Could not create $zipPath", 'red');
    exit(1);
}
foreach ($upload as $file) {
    $zip->addFile($file, $file);
}
$zip->close();

// ---------- upload instructions ----------
$steps = [];
$steps[] = 'FM Wholesale ERP - update ' . $stamp;
$steps[] = 'Changes since: ' . $base . '   (' . count($upload) . ' files)';
$steps[] = str_repeat('-', 60);
$n = 1;
if ($migrations) {
    $steps[] = $n++ . '. BACK UP THE LIVE DATABASE first (cPanel > Backup or phpMyAdmin > Export).';
}
$steps[] = $n++ . '. Upload ' . basename($zipPath) . ' to the project folder on the server';
$steps[] = '   (the folder that contains artisan) and Extract it there. Allow it to overwrite files.';
$steps[] = '   Then delete the zip from the server.';
if ($deleted) {
    $steps[] = $n++ . '. Delete these files on the server (they were removed in this update):';
    foreach ($deleted as $d) {
        $steps[] = '      ' . $d;
    }
}
$steps[] = $n++ . '. In cPanel > Terminal, go to the project folder and run:';
if ($composer) {
    $steps[] = '      composer install --no-dev --optimize-autoloader';
}
if ($migrations) {
    $steps[] = '      php artisan migrate --force';
}
$steps[] = '      php artisan optimize:clear';
$steps[] = '      php artisan config:cache';
$steps[] = '      php artisan route:cache';
$steps[] = '      php artisan view:cache';
$steps[] = $n++ . '. Open the site and check the pages you changed.';
$steps[] = $n++ . '. On your PC run:  deploy-patch.bat done';
$steps[] = '';
$steps[] = 'NEVER run migrate:fresh or --seed on live - they delete all data.';
if ($migrations) {
    $steps[] = '';
    $steps[] = 'New database changes in this update: ' . implode(', ', $migrations);
}
$steps[] = '';
$steps[] = 'Files in this update:';
foreach ($upload as $u) {
    $steps[] = '   ' . $u;
}
file_put_contents("deploy/patch-$stamp-STEPS.txt", implode(PHP_EOL, $steps) . PHP_EOL);

line();
line("Update package ready: $zipPath  (" . count($upload) . ' files)', 'green');
line("Steps saved to:       deploy/patch-$stamp-STEPS.txt", 'green');
line();
foreach (array_slice($steps, 3) as $s) {
    if ($s === 'Files in this update:') {
        break;
    }
    line($s, str_starts_with($s, 'NEVER') || str_contains($s, 'BACK UP') ? 'yellow' : '');
}
