<?php

namespace flight\util;

/**
 * FileCache - store object on file system
 *
 * Examples
 * Cache::setCacheDir('Cache');
 * Cache::setObject('obj', $obj, 100);
 * Cache::getObject('obj');
 *
 */
class FileCache
{

    private static $dir = '';

    public static $error = '';

    private static function filename ($str)
    {
        return base64_encode ($str);
    }

    /**
     * setObject - add object to cache
     *
     * @param $id_str_name
     * @param $object
     * @param $time_in_sec - defaults 180
     * @return boolean
     */
    public static function setObject ($id_str_name, $object, $time_in_sec = 180)
    {
        return (file_put_contents (self::$dir . '/' . self::filename($id_str_name), $time_in_sec . PHP_EOL . serialize($object))!== false);
    }

    /**
     * getObject - get object from cache or false if object has been expired or does not exists
     *
     * @param $id_str_name
     * @return object or false
     */
    public static function getObject ($id_str_name)
    {
        $path = self::$dir . '/' . self::filename($id_str_name);
        $str = file ($path);

        if ($str === false) return false;

        if ((time() - filemtime($path)) > intval($str[0])) return false;

        return unserialize($str[1]);
    }

    /**
     * setCacheDir - set cache directory
     *
     * @param $path
     * @return bool
     */
    public static function setCacheDir($path)
    {
        self::$dir = $path;

        if (!file_exists(self::$dir)) return mkdir(self::$dir);

        return is_dir(self::$dir);
    }

    /**
     *  clear - clear all cache files
     */
    public static function clear()
    {
        $it = new RecursiveDirectoryIterator(self::$dir);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {

            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

    }
}