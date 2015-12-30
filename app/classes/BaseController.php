<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 08.11.15
 * Time: 01:56
 */

abstract class BaseController {

    protected $view;
    protected $layout = 'default';
    protected $args = [];
    protected $noAuth = null;

    // в конструкторе подключаем шаблоны
    public function __construct()
    {
        $this->args = func_get_args();
        $this->view = new View($this->layout, get_class($this));
        if (is_null($this->noAuth) && empty(Registry::get('user'))) {
            $_SESSION['from_url'] = Tools::url($_SERVER['REQUEST_URI']);
            $_SESSION['redirect_message'] = json_encode(['type' => 'info', 'message' => 'Вы не авторизованы в системе!<br>Пожалуйста, нажмите на кнопку &quot;Войти в систему&quot;']);
            Tools::redirect();
        }
    }

    abstract function index();

    public function getParams()
    {
        $params = [];

        foreach ($this->args[0]['get'] as $key => $val) {
            $params[$key] = $val;
        }

        foreach ($this->args[0]['post'] as $key => $val) {
            $params[$key] = $val;
        }

        return $params;
    }

    public function getParam($param, $default = null)
    {
        $params = $this->getParams();

        if (empty($params[$param])) return $default;

        return is_array($params[$param]) ? $params[$param] : (empty(trim($params[$param])) ? $default : trim($params[$param]));
    }

    public function getPost()
    {
        return $this->args[0]['post'];
    }

    public function getGet()
    {
        return $this->args[0]['get'];
    }
}