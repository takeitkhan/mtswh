<?php

// Path to your Laravel project (current folder)
$laravelPath = __DIR__;

// Get Laravel version
$laravelVersion = null;

// Try to get version via Artisan
$artisanFile = $laravelPath . '/artisan';
if (file_exists($artisanFile)) {
    $output = null;
    $returnVar = null;
    exec("php artisan --version", $output, $returnVar);
    if ($returnVar === 0 && !empty($output)) {
        $laravelVersion = trim($output[0]);
    }
}

// Fallback: read composer.lock
if (!$laravelVersion && file_exists($laravelPath . '/composer.lock')) {
    $composerLock = json_decode(file_get_contents($laravelPath . '/composer.lock'), true);
    foreach ($composerLock['packages'] as $package) {
        if ($package['name'] === 'laravel/framework') {
            $laravelVersion = 'Laravel ' . $package['version'];
            break;
        }
    }
}

// Prepare README content
$readmeContent = "# Project: " . basename($laravelPath) . "\n\n";
$readmeContent .= $laravelVersion ? "Laravel Version: {$laravelVersion}\n" : "Laravel Version: Unknown\n";
$readmeContent .= "\n## Description\nDescribe your project here.\n";

// Write README.md
file_put_contents($laravelPath . '/README.md', $readmeContent);

echo "README.md generated successfully.\n";
