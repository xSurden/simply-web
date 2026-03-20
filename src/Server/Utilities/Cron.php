<?php

    namespace SW\Source\Server\Utilities;

    use SW\Source\Server\CLI\Maintenance;

    class Cron {

        private static $Cron_Tasks_File = ABSPATH . "/cron_tasks.json";

        public static function Run(bool $log = true) {
            if (Maintenance::Status()) {
                if ($log) echo "Cron skipped: System is in Maintenance Mode.\n";
                return;
            }

            $tasks = self::FetchTasks();

            foreach ($tasks as $task) {
                $class = $task['class'] ?? null;
                $method = $task['method'] ?? null;

                if ($class && $method && is_callable([$class, $method])) {
                    try {
                        call_user_func([$class, $method]);
                        if ($log) echo "Executed: {$class}::{$method}\n";
                    } catch (\Exception $e) {
                        if ($log) echo "Error executing {$class}::{$method}: " . $e->getMessage() . "\n";
                    }
                }
            }

            if ($log) echo "Cron run finished at " . date('Y-m-d H:i:s') . "\n";
        }

        public static function Register(string $class, string $method) {
            $tasks = self::FetchTasks();

            foreach ($tasks as $task) {
                if ($task['class'] === $class && $task['method'] === $method) {
                    return false; 
                }
            }

            $tasks[] = [
                'class' => $class,
                'method' => $method
            ];

            return file_put_contents(
                self::$Cron_Tasks_File, 
                json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
        }

        private static function FetchTasks(): array {
            if (file_exists(self::$Cron_Tasks_File)) {
                $file_contents = file_get_contents(self::$Cron_Tasks_File);
                return json_decode($file_contents, true) ?? [];
            }
            return [];
        }
    }