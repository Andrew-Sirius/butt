<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 08.11.15
 * Time: 01:42
 */
class Admin extends BaseController
{
    protected $layout = 'admin';

    public function index()
    {
        $this->view->userInfo = ['name' => 'Andrew', 'pass' => '123456'];
        $this->view->view('index');
    }
}