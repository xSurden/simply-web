<?php

    namespace SW\Source\Server\Utilities;

    class Cron {

        private static $Cron_Tasks_File = ABSPATH . "/cron_tasks.json";

        public static function Run(bool $log = true) {
            $tasks = self::FetchTasks();

            foreach ($tasks as $task) {
                if (is_callable([$task->class, $task->method])) {
                    call_user_func([$task->class, $task->method]);
                    
                    if ($log) {
                        echo "Executed: {$task->class}::{$task->method} \n";
                    }
                }
            }

            if ($log) {
                echo "Cron run finished at " . date('Y-m-d H:i:s') . "\n";
            }
        }

        public static function Register(string $class, string $method) {
            $tasks = self::FetchTasks();

            foreach ($tasks as $task) {
                if ($task->class === $class && $task->method === $method) {
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