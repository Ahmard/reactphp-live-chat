<?php


namespace App\Http\View;

use Exception;
use Mustache_Engine;
use function React\Promise\Stream\unwrapReadable;

class View
{
    public static function load(string $viewFile, array $data =[])
    {
        $foundView = self::find($viewFile);
        if($foundView){
            return self::render($foundView, $data);
        }

        return self::render($foundView, $data);
    }

    /**
     * @param string $viewFilePath
     * @return string
     * @throws Exception
     */
    public static function find(string $viewFilePath)
    {
        $viewFile = view_path($viewFilePath);

        if(file_exists($viewFile)){
            return $viewFile;
        }
        throw new Exception("View file($viewFile) not found");
    }

    /**
     * Render string(view file source)
     * @param string $viewFile
     * @param array $data
     * @return false|string
     */
    protected static function render(string $viewFile, array $data)
    {
        ob_start();

        extract($data);

        require $viewFile;

        $html = ob_get_contents();

        ob_end_clean();

        return $html;
    }
}