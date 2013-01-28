<?php

namespace flight\util;

/**
 * Created by Anton Lazarchenko.
 * Date: 25.01.13
 */
class UrlManager
{

    /**
     * @var array $routes - configuration routes array
     */
    public static $routes;

    /**
     * @var string $absoluteUrl
     */
    public static $absoluteUrl;


    /**
     * @param $routes - configuration routes array
     */
    public function __construct ($routes)
    {
        self::$routes = $routes;
    }


    /**
     * Method check route and parameters for matching routes
     * and generate link with parameters in order of matched rule.
     *
     * @param string $route - example '/controllers/ClassName'
     * @param array $params
     * @return string
     */
    public static function createUrl($route, $params = array())
    {

        if (empty(self::$routes))
            throw new \ErrorException('Routes absent.', '21', 1, __FILE__, __LINE__);

        $routes = self::$routes;

        $routeExplode = explode('/', $route);
        $route = implode('\\', $routeExplode);

        $pattern = '/' . addslashes($route) . '/is';

        $matches = array();
        foreach ($routes as $routeKey => $routeVal) {
            $res = preg_match($pattern, $routeVal[0]);
            if ($res) {
                $tmp['controller'] = $routeVal[0];
                $tmp['action'] = $routeVal[1];
                $tmp['pattern'] = $routeKey;
                $matches[] = $tmp;
            }
        }

        if (empty($matches))
            throw new \ErrorException('Incorrect specifying of url.', '22', 1, __FILE__, __LINE__);


        for ($m = 0; $m < count($matches); $m++) {
            $matchParam[$m] = array();
            foreach ($params as $paramKey => $paramVal) {
                $matchParam[$m][] = preg_match('/@' . $paramKey . '/', $matches[$m]['pattern']);
            }

            if (array_product($matchParam[$m])) {
                $resultRule[] = $matches[$m];
            }
        }

        if (empty($resultRule))
            throw new \ErrorException('No rules matched for creating links.', '23', 1, __FILE__, __LINE__);

        $paramPos = array();
        foreach ($params as $paramKey => $paramVal) {
            for ($n = 0; $n < count($resultRule); $n++) {
                if (substr_count($resultRule[$n]['pattern'], '@') == count($params))
                    $paramPos[strpos($resultRule[$n]['pattern'], $paramKey)] = $paramKey;
            }
        }

        if (count($paramPos) != count($params))
            throw new \ErrorException('Incorrect specifying of parameters quantity.', '24', 1, __FILE__, __LINE__);

        ksort($paramPos);
        $paramPos = array_values($paramPos);


        $uri = '';
        foreach ($paramPos as $v)
            $uri .='/' . $params[$v];

        return self::getAbsoluteUrl() . $uri;

    }


    /**
     * @return string - absolute url
     */
    public static function getAbsoluteUrl ()
    {

        if (self::$absoluteUrl)
            return self::$absoluteUrl;

        $host = 'http://' . $_SERVER['HTTP_HOST'];
        $path = $_SERVER['SCRIPT_NAME'];

        $url = $host . $path;

        if (basename($_SERVER['SCRIPT_NAME']) === basename($_SERVER['SCRIPT_FILENAME']))
            $url = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $url);
        elseif (strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT'])===0) {
            $url = $host . str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
            $url = str_replace(basename($url), '', $url);
        } else {
            if (preg_match('/\.php|\.htm/is', $url)) {
                $url = str_replace(basename($url), '', $url);
            }
        }

        $url = rtrim($url, '/');
        self::$absoluteUrl = $url;

        return $url;
    }


}
