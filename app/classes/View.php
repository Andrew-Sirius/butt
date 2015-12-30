<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 08.11.15
 * Time: 02:01
 */

class View {

    private $template;
    private $controller;
    private $layout;
    private $data = array();

    function __construct($layout, $controllerName)
    {
        $this->layout = $layout;
        $this->controller = $controllerName;
    }
    
    public function __set($name, $value)
    {
        if (!empty($this->data[$name])) {
            trigger_error ('Unable to set var `' . $name . '`. Already set, and overwrite not allowed.', E_USER_NOTICE);
            return false;
        }
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return empty($this->data[$name]) ? false : $this->data[$name];
    }

    // отображение
    function view($template)
    {
        $this->template = $template;

        $pathLayout = SITEPATH . 'views' . DS . 'layouts' . DS . $this->layout . '.php';
        if (!file_exists($pathLayout)) {
            trigger_error ('Layout `' . $this->layout . '` does not exist.', E_USER_NOTICE);
            return false;
        }

        foreach ($this->data as $key => $value) {
            $$key = $value;
        }

        require_once $pathLayout;
    }

    public function template()
    {
        $contentPage = SITEPATH . 'views' . DS . strtolower($this->controller) . DS . $this->template . '.php';

        if (!file_exists($contentPage)) {
            trigger_error ('Template `' . $this->template . '` does not exist.', E_USER_NOTICE);
            return false;
        }

        require_once $contentPage;
    }

}
