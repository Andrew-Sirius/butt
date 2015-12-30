<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 07.11.15
 * Time: 18:13
 */
class Router
{
    private $path;

    public function __construct(){}

    public function setPath($path)
    {
        $path = $path . DS;

        if (!is_dir($path)) {
            throw new Exception ('Invalid controller path: `' . $path . '`');
        }

        $this->path = $path;
    }

    public function delegate()
    {
        // Анализируем путь
        $this->getController($file, $controller, $action, $args);

        // Файл доступен?
        if (!is_readable($file)) exit('Controller\'s file is not readable!');

        // Подключаем файл
        require_once ($file);

        // Создаём экземпляр контроллера
        $controller = new $controller($args);

        // Действие доступно?
        if (!is_callable([$controller, $action])) {
            $err = new Error();
            $err->error(404);
        }

        // Выполняем действие
        $controller->$action();
    }

    private function getController(&$file, &$controller, &$action, &$args)
    {
        $route = empty($_GET['route']) ? 'index/index' : $_GET['route'];
        $post = empty($_POST) ? null : $_POST;
        $route = Registry::get('maintenance') ? 'index/maintenance' : $route;

        // Получаем раздельные части
        $route = trim($route, '/\\');
        $parts = explode('/', $route);

        // Находим правильный контроллер
        $cmd_path = $this->path;

        $controller = ucfirst(array_shift($parts));
        if (!is_file($cmd_path . $controller . '.php')) $controller = 'Index';

        // Получаем действие
        $action = array_shift($parts);
        $action = empty($action) ? 'index' : $action;

        if (!empty($parts)) {
            foreach ($parts as $key => $val) {
                if (($key+1) % 2) {
                    $get[$val] = empty($parts[$key + 1]) ? null : $parts[$key + 1];
                }
            }
        }

        $file = $cmd_path . $controller . '.php';
        $args['get'] = empty($get) ? [] : $get;
        $args['post'] = empty($post) ? [] : $post;
    }
}