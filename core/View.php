<?php

namespace Core;

class View {
    private static ?string $layout = null;
    private static array $sections = [];
    private static ?string $currentSection = null;

    public static function render(string $view, array $data = []): string {
        $viewPath = self::resolvePath($view);

        if (!file_exists($viewPath)) {
            throw new \Exception("View [{$view}] tidak ditemukan di {$viewPath}");
        }

        extract($data);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // If a layout was declared inside the view
        if (self::$layout) {
            $layoutPath = self::resolvePath(self::$layout);
            self::$layout = null;

            if (file_exists($layoutPath)) {
                // If section was not used, default content goes to 'content' section
                if (!isset(self::$sections['content'])) {
                    self::$sections['content'] = $content;
                }

                ob_start();
                require $layoutPath;
                $content = ob_get_clean();
            }
        }

        self::$sections = [];
        return $content;
    }

    public static function layout(string $layout): void {
        self::$layout = $layout;
    }

    public static function startSection(string $name): void {
        self::$currentSection = $name;
        ob_start();
    }

    public static function endSection(): void {
        if (self::$currentSection === null) {
            throw new \Exception("Tidak ada section yang sedang dibuka.");
        }
        self::$sections[self::$currentSection] = ob_get_clean();
        self::$currentSection = null;
    }

    public static function yield(string $name, string $default = ''): string {
        return self::$sections[$name] ?? $default;
    }

    private static function resolvePath(string $view): string {
        $normalized = str_replace('.', '/', $view);
        return dirname(__DIR__) . '/app/Views/' . $normalized . '.php';
    }
}
