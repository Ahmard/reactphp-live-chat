<?php


namespace App\Core\Http\View;

use Exception;

class View
{
    public static function load(string $viewFile, array $data = []): string
    {
        if (!strpos($viewFile, '.php')) {
            $viewFile .= '.php';
        }

        $foundView = self::find($viewFile);
        if ($foundView) {
            return self::render($foundView, $data);
        }

        return self::render($foundView, $data);
    }

    /**
     * @param string $viewFilePath
     * @return string
     * @throws Exception
     */
    public static function find(string $viewFilePath): string
    {
        $viewFile = view_path($viewFilePath);

        if (file_exists($viewFile)) {
            return $viewFile;
        }

        throw new Exception("View file($viewFile) not found");
    }

    /**
     * Render string(view file source)
     * @param string $viewFile
     * @param array $data
     * @return string
     */
    protected static function render(string $viewFile, array $data): string
    {
        ob_start();

        extract($data);

        require $viewFile;

        $html = ob_get_contents();

        ob_end_clean();

        return (string)$html;
    }
}