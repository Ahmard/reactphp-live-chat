<?php


namespace App\Core\Helpers\Classes;


use Psr\Http\Message\ServerRequestInterface;
use WyriHaximus\React\Http\Middleware\Session;
use WyriHaximus\React\Http\Middleware\SessionMiddleware;

class SessionHelper
{
    protected static ServerRequestInterface $request;
    protected static self $instance;
    protected Session $session;
    protected array $sessionData;

    public function __construct()
    {
        $this->session = self::$request->getAttribute(SessionMiddleware::ATTRIBUTE_NAME);

        $this->sessionData = $this->session->getContents();
    }

    public static function setRequest(ServerRequestInterface $request)
    {
        self::$request = $request;
    }

    public static function getInstance(): self
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }

        return self::$instance = new self();
    }

    /**
     * @return array
     */
    public function getSessionData(): array
    {
        return $this->sessionData;
    }

    /**
     * Set session data
     * @param string|array $key
     * @param null $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->sessionData = array_merge($this->sessionData, $key);
        } else {
            $this->sessionData[$key] = $value;
        }

        return $this;
    }

    /**
     * Retrieve session data
     * @param string|array $key
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->sessionData[$key] ?? null;
    }

    /**
     * Delete session data
     * @param string|array $key
     * @return $this
     */
    public function delete($key)
    {
        if (is_array($key)) {
            foreach ($key as $sessionName) {
                unset($this->sessionData[$sessionName]);
            }
        } else {
            unset($this->sessionData[$key]);
        }

        return $this;
    }
}