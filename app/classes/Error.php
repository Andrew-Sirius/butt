<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 09.11.15
 * Time: 16:22
 */
class Error
{
    protected $layout = 'error';
    protected $view;

    public function __construct()
    {
        $this->view = new View($this->layout, get_class($this));
    }

    public function error($err)
    {
        switch ($err) {
            case 404:
                $this->view->pageTitle = 'Страница не найдена - ' . Registry::get('site_name');
                break;

            default:
                $this->view->pageTitle = 'Неизвестная ошибка - ' . Registry::get('site_name');
        }

        $this->view->pageDescription = 'Страница ошибки на сайте';

        $this->view->view('error');
    }
}