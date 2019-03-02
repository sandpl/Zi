<?php
/**
 * @filename Request.php
 *
 * @author zhangjie <zhangjie@wfzczj@foxmail.com>
 * @date: 19-2-25
 */


class Request
{
    private $isConsoleRequest;

    private $cookies;

    private $headers;


    /**
     * @return bool
     */
    public function isConsoleRequest()
    {
        return $this->isConsoleRequest !== null ? $this->isConsoleRequest : $this->isConsoleRequest = PHP_SAPI === 'cli';
    }

    public function getScriptFile()
    {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            return $_SERVER['SCRIPT_FILENAME'];
        } else {

        }
    }

    //request 里面有啥？   $_GET $_POST $_REQUEST
    public function request()
    {

    }


    public function getContentType()
    {
        if (isset($_SERVER['CONTENT_TYPE'])) {
            return $_SERVER['CONTENT_TYPE'];
        }


    }

    public function getMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return $_SERVER['REQUEST_METHOD'];
        }
    }

    public function getRawBody()
    {
        return file_get_contents('php://input');
    }

    public function getIsAjax()
    {
        return $this->headers->get('X-requested-with') === "XMLHttpRequest";
    }


    public function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    }

    public function getServerName()
    {
        return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
    }

    public function getServerPort()
    {
        return isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '';
    }


    public function getRemoteAddr()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }


    public function getRemoteHost()
    {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
    }


    //TODO 每一个cookie摄制成一个对象，所有cookie封装成一个cookieCollection
    public function getCookies()
    {
        return $_COOKIE;
    }

    public function getIsSecureConnection()
    {
        if (isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1) ) {
            return true;
        }
        return false;
    }

    public function get($name = null, $defaultValue = null)
    {
        if ($name == null) {
            return $_GET;
        } else {
            return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
        }

    }

    public function post($name = null, $defaultValue = null)
    {
        if ($name == null) {
            return $this->getBodyParams();
        } else {
            return isset($this->getBodyParams()[$name]) ? $this->getBodyParams()[$name] : $defaultValue;
        }
    }

    public function getBodyParams()
    {
        $contentType = $this->getContentType();
        if (($pos = strpos($contentType, ';')) !== false) {
            // e.g. text/html; charset=UTF-8
            $contentType = substr($contentType, 0, $pos);
        } else {
            $contentType = $contentType;
        }
        if ($contentType == 'text/json') {
            //TODO handle
            return json_decode($this->getRawBody(), true);
        } else {
            return $_POST;
        }

    }



}