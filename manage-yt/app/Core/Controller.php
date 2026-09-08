<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller {
    protected function view(string $view, array $data = [], string $layout = 'app'): void {
        extract($data);
        $viewPath = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!is_file($viewPath)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $layoutPath = dirname(__DIR__) . '/Views/layouts/' . $layout . '.php';
        if (is_file($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    protected function json(array $data, int $status = 200): never {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
        exit;
    }
}