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

        case "update":
            UpdateSystem();
            break;

        case "change-repo":
            ChangeRepo($target);
            break;

        case "maintenance":
            Maintenance($target);
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

    function Maintenance($target = null) {
        $Package = new \SW\Source\Server\CLI\Maintenance();

        if ($target === null) {
            $Package->Toggle("disable");
            echo "Maintenance mode was not specified - defaulted to DISABLED (off)\n";
            return;
        }

        $input = strtolower((string)$target);
        
        if ($input === "enable" || $input === "disable") {
            if ($Package->Toggle($input)) {
                echo "Successfully " . strtoupper($input) . "D maintenance mode.\n";
                if ($input === "enable") {
                    echo "File created at: /server/maintenance\n";
                } else {
                    echo "File removed from: /server/maintenance\n";
                }
            } else {
                echo "Failed to " . $input . " maintenance mode in database, though file state may have changed.\n";
            }
        } else {
            echo "Usage: sw maintenance [enable|disable]\n";
        }
    }

    function ChangeRepo($url) {
        // Init pointer
        $Pointer = new \SW\Source\Modules\SimplySql\Pointer();

        if ($url === null) {
            echo "No Repo link was provided.";
        } else {
            try {
                $new = [
                    "config_value" => $url ?? "https://repo.surden.me/packages/"
                ];
                $where = [
                    "config_key" => "package_repository_url"
                ];
                $result = $Pointer->Update("server_configs", $new, $where);
                if ($result) {
                    echo "Updated the repository's location to: " . $url;
                } else {
                    echo "System update failed at one or more stages.\n";
                }
            } catch (\Exception $e) {
                echo "Critical Repo Change Error: " . $e->getMessage() . "\n";
            }
        }
    }

    function UpdateSystem() {
        echo "--- Updating the system ---\n";
        $manager = new \SW\Source\Server\PackageManger();

        try {
            $success = $manager->UpdateSystem();
            
            if ($success) {
                echo "Successfully updated all system components.\n";
            } else {
                echo "System update failed at one or more stages.\n";
            }
        } catch (\Exception $e) {
            echo "Critical Update Error: " . $e->getMessage() . "\n";
        }
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