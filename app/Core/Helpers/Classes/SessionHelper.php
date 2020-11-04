<?php


namespace App\Core\Helpers\Classes;


use Niko9911\React\Middleware\SessionMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class SessionHelper
{
    protected static ServerRequestInterface $request;

    protected static self $instance;
    protected static bool $isFresh = true;
    protected $session;
    protected array $sessionData;

    public function __construct()
    {
        $this->session = self::$request->getAttribute(SessionMiddleware::ATTRIBUTE_NAME);

        $this->sessionData = $this->session->getContents();
    }

    public static function getInstance(): self
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }

        return self::$instance = new self();
    }

    public static function setRequest(ServerRequestInterface $request)
    {
        self::$request = $request;
    }

    public function register()
    {
        if (!self::$isFresh) {
            self::$request->getAttribute(SessionMiddleware::ATTRIBUTE_NAME)
                ->setContents(self::getSessionData());
        }

        self::$isFresh = true;
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
        self::$isFresh = false;

        if (is_array($key)) {
            $this->sessionData = array_merge($this->sessionData, $key);
        } else {
            $this->sessionData[$key] = $value;
        }

        return $this;
    }

    /**
     * Check if session has some key
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return array_key_exists($key, $this->sessionData);
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