<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 08.11.15
 * Time: 00:47
 */
class Index extends BaseController
{
    protected $layout = 'index';

    public function __construct()
    {
        $this->view = new View($this->layout, get_class($this));
    }

    public function index()
    {
        if (!empty(Registry::get('user'))) Tools::redirect('blog/index');

        $this->view->pageDescription = 'Система набора и учёта баллов';
        $this->view->pageTitle = Registry::get('site_name');
        $this->view->view('index');
    }

    public function about()
    {
        $this->view->pageDescription = 'Информация о сайте мотивационной системы';
        $this->view->pageTitle = 'Что это? - ' . Registry::get('site_name');
        $this->view->view('about');
    }

    public function contact()
    {
        $this->view->pageDescription = 'Контактная информация';
        $this->view->pageTitle = 'Связаться с нами - ' . Registry::get('site_name');
        $this->view->view('contact');
    }

    public function maintenance()
    {
        $this->view->pageDescription = 'Сайт мотивационной системы закрыт на обслуживание';
        $this->view->pageTitle = 'Сайт на обслуживании - ' . Registry::get('site_name');
        $this->view->view('maintenance');
    }
}