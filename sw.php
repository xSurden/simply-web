#!/usr/bin/php
<?php

    if (!defined('ABSPATH')) {
        define('ABSPATH', __DIR__);
    }

    require_once __DIR__ . '/vendor/autoload.php'; 

    use SW\Source\Server\PackageManger;

    $command = $argv[1] ?? null;
    $target  = $argv[2] ?? null;

    if (!$command) {
        die("Usage: sw [command] [target]\n");
    }

    switch ($command) {
        case 'install':
            InstallHandler($target);
            break;

        case "help":
            echo "Simply-Web CLI\n";
            echo "Usage: sw [command] [target]\n";
            echo "Commands:\n";
            echo "  install [package] - Installs a package from the repository\n";
            echo "  help              - Displays this help message\n";
            echo "\nIf you have a package installed, running the install command again can update the current package.";
            echo "Install command can also be used to replace broken packages by re-downloading and overwriting the files.";
            break;

        default:
            echo "Unknown command: $command\n";
            break;
    }

    function InstallHandler($target) {
        if (!$target) {
            die("Error: Please specify a package (e.g., sw install test-package)\n");
        }
        
        echo "--- Starting Installation ---\n";
        
        $manager = new PackageManger();
        
        try {
            $success = $manager->InstallPackage($target);
            
            if ($success) {
                echo "Successfully installed $target!\n";
            } else {
                echo "Installation failed for $target.\n";
            }
        } catch (\Exception $e) {
            echo "Critical Error: " . $e->getMessage() . "\n";
        }
    }