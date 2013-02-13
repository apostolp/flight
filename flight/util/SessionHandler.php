<?php

namespace flight\util;

/**
 * Class SessionHandler provides management of session data.
 *
 * Created by Anton Lazarchenko.
 * Date: 28.01.13
 *
 */
class SessionHandler implements \IteratorAggregate, \ArrayAccess, \Countable
{

    /**
     * @var string
     */
    protected static $savePath;

    /**
     * @var string
     */
    protected static $sessionName;

    /**
     * @var int
     */
    protected static $lifetime;


    /**
     * Setting configuration and start session in case autoStart option.
     * @param array $config
     */
    public function __construct($config)
    {
        $this->setConfig($config);
        $this->open();
    }


    /**
     * Setting configuration parameters.
     * @param array $config
     */
    protected function setConfig ($config)
    {
        if (isset($config['savePath'])) {
            self::$savePath = rtrim(str_replace(basename($_SERVER['SCRIPT_FILENAME']),
                                '', $_SERVER['SCRIPT_FILENAME']), '/') . $config['savePath'];

            if (!is_dir(self::$savePath))
                mkdir(self::$savePath, 0777);

            self::$savePath = realpath(self::$savePath);

            ini_set('session.save_path', self::$savePath);
        }

        if (isset($config['sessionName'])) {
            self::$sessionName = $config['sessionName'];
            ini_set('session.name', self::$sessionName);
        }

        if (isset($config['lifetime'])) {
            self::$lifetime = $config['lifetime'];
            if (strstr(self::$lifetime, '*')) {
                $product = explode('*', self::$lifetime);
                self::$lifetime = array_product($product);
            }
            ini_set('session.gc_maxlifetime', self::$lifetime);
            ini_set('session.cookie_lifetime', self::$lifetime);
        }

    }




    /**
     * Returns a value indicating whether to use custom session storage.
     * This method should be overriden to return true if custom session storage handler should be used.
     * If returning true, make sure the methods {@link openSession}, {@link closeSession}, {@link readSession},
     * {@link writeSession}, {@link destroySession}, and {@link gcSession} are overridden in child
     * class, because they will be used as the callback handlers.
     * The default implementation always return false.
     * @return boolean whether to use custom storage.
     */
    public function getUseCustomStorage()
    {
        return false;
    }


    /**
     * Starts session in case it not started already.
     * @throws \ErrorException
     */
    public function open()
    {
        if($this->getUseCustomStorage()) {
            @session_set_save_handler(array($this, 'openSession'), array($this, 'closeSession'),
                array($this, 'readSession'), array($this, 'writeSession'),
                array($this, 'destroySession'), array($this, 'gcSession'));
        }

        register_shutdown_function(array($this, 'close'));

        @session_start();
        @session_name(self::$sessionName);


        if (!session_id())
            throw new \ErrorException('Session has not started.', '31', 1, __FILE__, __LINE__);

    }

    /**
     * Ends the current session and store session data.
     */
    public function close()
    {
        if(session_id()!=='')
            @session_write_close();
    }

    /**
     * Unset all variables and destroy session.
     */
    public function destroy()
    {
        if(session_id()!=='')
        {
            @session_unset();
            @session_destroy();
        }
    }



    /**
     * Session open handler.
     * This method should be overridden if {@link getUseCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $savePath session save path
     * @param string $sessionName session name
     * @return boolean whether session is opened successfully
     */
    public function openSession($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Session close handler.
     * This method should be overridden if {@link getUseCustomStorage} is set true.
     * Do not call this method directly.
     * @return boolean whether session is closed successfully
     */
    public function closeSession()
    {
        return true;
    }

    /**
     * Session read handler.
     * This method should be overridden if {@link getUseCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $id session ID
     * @return string the session data
     */
    public function readSession($id)
    {
        return '';
    }

    /**
     * Session write handler.
     * This method should be overridden if {@link getUseCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $id session ID
     * @param string $data session data
     * @return boolean whether session write is successful
     */
    public function writeSession($id, $data)
    {
        return true;
    }

    /**
     * Session destroy handler.
     * This method should be overridden if {@link getUseCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $id session ID
     * @return boolean whether session is destroyed successfully
     */
    public function destroySession($id)
    {
        return true;
    }

    /**
     * Session GC (garbage collection) handler.
     * This method should be overridden if {@link getUseCustomStorage} is set true.
     * Do not call this method directly.
     * @param integer $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
     * @return boolean whether session is GCed successfully
     */
    public function gcSession($maxLifetime)
    {
        return true;
    }



    /**
     * @return string session id
     */
    public function getID ()
    {
        return session_id();
    }

    /**
     * @return string session name
     */
    public function getName ()
    {
        return session_name();
    }

    protected function getLifetime ()
    {
        return self::$lifetime;
    }


    /**
     * Returns an iterator for traversing the session variables.
     * This method is required by the interface IteratorAggregate.
     * @return SessionHandler an iterator for traversing the session variables.
     */
    public function getIterator()
    {
        return new SessionHandlerIterator;
    }


    /**
     * Returns the number of items in the session.
     * @return integer the number of session variables
     */
    public function getCount()
    {
        return count($_SESSION);
    }

    /**
     * Returns the number of items in the session.
     * This method is required by Countable interface.
     * @return integer number of items in the session.
     */
    public function count()
    {
        return $this->getCount();
    }

    /**
     * @return array the list of session variable names
     */
    public function getKeys()
    {
        return array_keys($_SESSION);
    }

    /**
     * Returns the session variable value with the session variable name.
     * This method is very similar to {@link itemAt} and {@link offsetGet},
     * except that it will return $defaultValue if the session variable does not exist.
     * @param mixed $key the session variable name
     * @param mixed $defaultValue the default value to be returned when the session variable does not exist.
     * @return mixed the session variable value, or $defaultValue if the session variable does not exist.
     */
    public function get($key,$defaultValue=null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }

    /**
     * Returns the session variable value with the session variable name.
     * This method is exactly the same as {@link offsetGet}.
     * @param mixed $key the session variable name
     * @return mixed the session variable value, null if no such variable exists
     */
    public function itemAt($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Adds a session variable.
     * Note, if the specified name already exists, the old value will be removed first.
     * @param mixed $key session variable name
     * @param mixed $value session variable value
     */
    public function add($key,$value)
    {
        $_SESSION[$key]=$value;
    }

    /**
     * Removes a session variable.
     * @param mixed $key the name of the session variable to be removed
     * @return mixed the removed value, null if no such session variable.
     */
    public function remove($key)
    {
        if(isset($_SESSION[$key]))
        {
            $value=$_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        }
        else
            return null;
    }

    /**
     * Removes all session variables
     */
    public function clear()
    {
        foreach(array_keys($_SESSION) as $key)
            unset($_SESSION[$key]);
    }

    /**
     * @param mixed $key session variable name
     * @return boolean whether there is the named session variable
     */
    public function contains($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * @return array the list of all session variables in array
     */
    public function toArray()
    {
        return $_SESSION;
    }

    /**
     * This method is required by the interface ArrayAccess.
     * @param mixed $offset the offset to check on
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    /**
     * This method is required by the interface ArrayAccess.
     * @param integer $offset the offset to retrieve element.
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    public function offsetGet($offset)
    {
        return isset($_SESSION[$offset]) ? $_SESSION[$offset] : null;
    }

    /**
     * This method is required by the interface ArrayAccess.
     * @param integer $offset the offset to set element
     * @param mixed $item the element value
     */
    public function offsetSet($offset,$item)
    {
        $_SESSION[$offset]=$item;
    }

    /**
     * This method is required by the interface ArrayAccess.
     * @param mixed $offset the offset to unset element
     */
    public function offsetUnset($offset)
    {
        unset($_SESSION[$offset]);
    }



}