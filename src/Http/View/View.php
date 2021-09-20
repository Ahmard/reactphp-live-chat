<?php


namespace Server\Http\View;

use Exception;
use Server\Http\Request;

class View
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function create(Request $request): View
    {
        return new View($request);
    }

    public function load(string $viewFile, array $data = []): string
    {
        if (!strpos($viewFile, '.php')) {
            $viewFile .= '.php';
        }

        $foundView = $this->find($viewFile);

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
    public function find(string $viewFilePath): string
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
    protected function render(string $viewFile, array $data): string
    {
        $data = array_merge([
            'request' => $this->request,
        ], $data);

        ob_start();

        extract($data);

        require $viewFile;

        $html = ob_get_contents();

        ob_end_clean();

        return (string)$html;
    }
}