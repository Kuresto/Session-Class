<?php
/**
 * Copyright (c) 2015 Nilton Teixeira
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without li`ation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace kuresto\Session;

use SessionHandler;

class Session extends SessionHandler
{
    protected $key;
    protected $name;
    protected $cookie;

    private $isSecure = false;

    /**
     * Constructor.
     */
    public function __construct() {
        if(session_id() == '')
            session_start();
    }

    public function secureSession($key = null, $name = null) {

        $this->isSecure = true;

        if(!empty($key))
            $this->key = 'KnF5fRpMNUJ461NCoPpcUO7c9rNn060hVPmXIoVZ';

        if(!empty($name))
            $this->name = "SecureSession";

        $this->cookie += ['lifetime' => 0, 'path' => ini_get('session.cookie_path'), 'domain' => ini_get('session.cookie_domain'), 'secure' => isset($_SERVER['HTTPS']), 'httponly' => true];

        $this->secureSessionSetup();

    }

    private function secureSessionSetup() {
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);

        session_name($this->name);

        session_set_cookie_params($this->cookie['lifetime'], $this->cookie['path'], $this->cookie['domain'], $this->cookie['secure'], $this->cookie['httponly']);
    }

    public function read($id) {
        return mcrypt_decrypt(MCRYPT_3DES, $this->key, parent::read($id), MCRYPT_MODE_ECB);
    }

    public function write($id, $data) {
        return parent::write($id, mcrypt_encrypt(MCRYPT_3DES, $this->key, $data, MCRYPT_MODE_ECB));
    }

    /**
     * Destructor.
     */
    public function __destruct() {
        unset($this);
    }

    /**
     * Register the session.
     *
     * @param integer $time .
     */
    public function register($time = 60) {
        $_SESSION['session_id']    = session_id();
        $_SESSION['session_time']  = intval($time);
        $_SESSION['session_start'] = $this->newTime();
    }

    /**
     * Checks to see if the session is registered.
     *
     * @return  True if it is, False if not.
     */
    public function isRegistered() {
        if(!empty($_SESSION['session_id'])) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Set key/value in session.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieve value stored in session by key.
     *
     * @var mixed
     * @return bool
     */
    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    /**
     * Retrieve the global session variable.
     *
     * @return array
     */
    public function getSession() {
        return $_SESSION;
    }

    /**
     * Gets the id for the current session.
     *
     * @return integer - session id
     */
    public function getSessionId() {
        return $_SESSION['session_id'];
    }

    /**
     * Checks to see if the session is over based on the amount of time given.
     *
     * @return boolean
     */
    public function isExpired() {
        if($_SESSION['session_start'] < $this->timeNow()) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Renews the session when the given time is not up and there is activity on the site.
     */
    public function renewTime() {
        $_SESSION['session_start'] = $this->newTime();
    }

    /**
     * @return int
     */
    private function timeNow() {
        $currentHour = date('H');
        $currentMin  = date('i');
        $currentSec  = date('s');
        $currentMon  = date('m');
        $currentDay  = date('d');
        $currentYear = date('y');

        return mktime($currentHour, $currentMin, $currentSec, $currentMon, $currentDay, $currentYear);
    }

    /**
     * @return int
     */
    private function newTime() {
        $currentHour = date('H');
        $currentMin  = date('i');
        $currentSec  = date('s');
        $currentMon  = date('m');
        $currentDay  = date('d');
        $currentYear = date('y');

        return mktime($currentHour, ($currentMin + $_SESSION['session_time']), $currentSec, $currentMon, $currentDay, $currentYear);
    }

    /**
     * Destroys the session
     */
    public function end() {

        if($this->isSecure) {
            $_SESSION = [];
            setcookie($this->name, '', time() - 42000, $this->cookie['path'], $this->cookie['domain'], $this->cookie['secure'], $this->cookie['httponly']);
        }

        session_destroy();
    }

    public function dump() {
        var_dump($this->getSession());
    }

    public function regenerate($destroy = false) {
        session_regenerate_id($destroy);
    }
}
