<?php

namespace flight\util;

/**
 * Created by Anton Lazarchenko
 * Date: 06.02.13
 *
 * Class SessionDbHandler provides management of session data through database.
 */
class SessionDbHandler extends SessionHandler
{

    /**
     * Database usage
     * @var
     */
    private static $db;

    /**
     * @var string
     */
    private static $tableName = 'session_data';

    /**
     * Setting configuration and start session in case autoTart option.
     * @param array $config
     */
    public function __construct($config)
    {
        self::$db = \Flight::$config['useDb']();
        self::createSessionTable(self::$tableName);
        parent::__construct($config);
    }



    /**
     * Returns a value indicating whether to use custom session storage.
     * This method overrides the parent implementation and always returns true.
     * @return boolean whether to use custom storage.
     */
    public function getUseCustomStorage()
    {
        return true;
    }

    /**
     * Creates the session DB table.
     * @param string $tableName the name of the table to be created
     */
    protected function createSessionTable($tableName)
    {
        $sql = "CREATE TABLE IF NOT EXISTS $tableName
            (id CHAR(32) PRIMARY KEY NOT NULL,
             expire INTEGER,
             data TEXT)
             ENGINE=MyISAM CHARSET=UTF8";
        self::$db->run($sql);
    }




    /**
     * Session open handler.
     * Do not call this method directly.
     * @param string $savePath session save path
     * @param string $sessionName session name
     * @return boolean whether session is opened successfully
     */
    public function openSession($savePath,$sessionName)
    {
        $now = time();
        self::$db->delete(self::$tableName, "expire<'$now'");
        return true;
    }

    /**
     * Session read handler.
     * Do not call this method directly.
     * @param string $id session ID
     * @return string the session data
     */
    public function readSession($id)
    {
        $now = time();
        $data = self::$db->select(self::$tableName, "id='$id' AND expire>'$now'", "", 'data');
        return $data === false ? '' : $data[0]['data'];
    }

    /**
     * Session write handler.
     * Do not call this method directly.
     * @param string $id session ID
     * @param string $data session data
     * @return boolean whether session write is successful
     */
    public function writeSession($id,$data)
    {
        if (!empty($data)) {
            $expire = time() + parent::getLifetime();

            if (self::$db->select(self::$tableName, "id='$id'", "", 'id') == false) {
                $info = array('id' => $id, 'data' => $data, 'expire' => $expire);
                self::$db->insert(self::$tableName, $info);
            } else {
                $info = array('data' => $data, 'expire' => $expire);
                self::$db->update(self::$tableName, $info, "id='$id'");
            }
        }
        return true;
    }

    /**
     * Session destroy handler.
     * Do not call this method directly.
     * @param string $id session ID
     * @return boolean whether session is destroyed successfully
     */
    public function destroySession($id)
    {
        self::$db->delete(self::$tableName, "id='$id'");
        return true;
    }

    /**
     * Session GC (garbage collection) handler.
     * Do not call this method directly.
     * @param integer $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
     * @return boolean whether session is GCed successfully
     */
    public function gcSession($maxLifetime)
    {
        $now = time();
        self::$db->delete(self::$tableName, "expire<$now");
        return true;
    }

}
