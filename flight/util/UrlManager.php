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
    public function __construct($routes)
    {
        self::$routes = $routes;
    }


    /**
     * Method check route and parameters for matching routes
     * and generate link with parameters in order of matched rule.
     *
     * @example Flight::urlManager()->createUrl('ControllerName/action', array('param1' => 'value1'));
     *
     * @param $route
     * @param array $params
     * @return string
     * @throws \ErrorException
     */
    public static function createUrl($route, $params = array())
    {

        if (empty(self::$routes)) {
            throw new \ErrorException('Routes absent.', '21', 1, __FILE__, __LINE__);
        }

        $configRoutes = self::$routes;

        $routeExplode = explode('/', $route);
        $controller   = $routeExplode[0];
        $action       = $routeExplode[1];

        $pattern = '/' . addslashes('\\controllers\\' . $controller) . '/is';

        $matches = array();
        foreach ($configRoutes as $configRouteKey => $configRouteVal) {
            $issetRoute  = preg_match($pattern, $configRouteVal[0]);
            $issetAction = preg_match('/' . $action . '/', $configRouteVal[1]);
            if ($issetRoute && $issetAction) {
                $tmp['controller'] = $configRouteVal[0];
                $tmp['action']     = $configRouteVal[1];
                $tmp['pattern']    = $configRouteKey;
                $matches[]         = $tmp;
            }
        }

        if (empty($matches)) {
            throw new \ErrorException('Incorrect specifying of url.', '22', 1, __FILE__, __LINE__);
        }

        for ($m = 0; $m < count($matches); $m++) {
            $matchParam[$m] = array();
            foreach ($params as $paramKey => $paramVal) {
                $matchParam[$m][] = preg_match('/@' . $paramKey . '/', $matches[$m]['pattern']);
            }

            if (array_product($matchParam[$m]) && substr_count($matches[$m]['pattern'], '@') == count($params)) {
                $resultRule[] = $matches[$m];
            }
        }

        if (empty($resultRule)) {
            throw new \ErrorException('No rules matched for creating links.', '23', 1, __FILE__, __LINE__);
        }

        $uri = $resultRule[0]['pattern'];

        foreach ($params as $paramKey => $paramVal) {
            $pattern = '/@' . $paramKey . '[^\/]*/';
            $uri     = preg_replace($pattern, $paramVal, $uri);
        }

        return self::getAbsoluteUrl() . $uri;

    }


    /**
     * @return string - absolute url
     */
    public static function getAbsoluteUrl()
    {

        if (self::$absoluteUrl) {
            return self::$absoluteUrl;
        }

        $prefix = (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://';
        $host   = $prefix . $_SERVER['HTTP_HOST'];
        $path   = $_SERVER['SCRIPT_NAME'];

        $url = $host . $path;

        if (basename($_SERVER['SCRIPT_NAME']) === basename($_SERVER['SCRIPT_FILENAME'])) {
            $url = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $url);
        } elseif (strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0) {
            $url = $host . str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
            $url = str_replace(basename($url), '', $url);
        } else {
            if (preg_match('/\.php|\.htm/is', $url)) {
                $url = str_replace(basename($url), '', $url);
            }
        }

        $url               = rtrim($url, '/');
        self::$absoluteUrl = $url;

        return $url;
    }


}
