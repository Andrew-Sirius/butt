<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 15.11.15
 * Time: 02:03
 */
class Blog extends BaseController
{
    public function index()
    {
        $this->view->blog = DB::run()->query('select b.*, u.name user_name from blog b
                                              left join users u on u.id=b.user_id
                                              where b.published_at is not null
                                              and b.deleted_at is null
                                              order by b.published_at desc')->fetchAll();


        $this->view->pub_dates = DB::run()->query('select substr(published_at,0,11) pub_date from blog
                                                  where published_at is not null
                                                  and deleted_at is null
                                                  GROUP BY substr(published_at,0,11)')->fetchAll();
        $this->view->pageDescription = 'Новости системы';
        $this->view->pageTitle = 'Новости системы - ' . Registry::get('site_name');
        $this->view->menu = 'system-news';
        $this->view->view('index');
    }

    public function article()
    {
        $tid = $this->getParam('show');

        if ($tid) {
            $article = DB::run()->query('select * from blog where tid = ' . DB::run()->quote($tid))->fetch();
            if ($article) {
                $this->view->article = $article;
                $this->view->pageDescription = 'Новости системы. ' . $article->title;
                $this->view->pageTitle = $article->title . ' - ' . Registry::get('site_name');
                $this->view->menu = 'system-news';
                $this->view->view('article');
                return;
            }
        }

        $err = new Error();
        $err->error(404);
    }

    public function about()
    {
        $user_id = (int) $this->getParam('author');

        if ($user_id) {
            $author = DB::run()->query('select u.name, u.registered_date, a.description from users u left join blog_authors a on a.user_id=u.id where u.id = ' . $user_id)->fetch();
            if ($author) {
                $this->view->author = $author;
                $this->view->pageDescription = 'Страница автора - ' . $author->name;
                $this->view->pageTitle = $author->name . ' - ' . Registry::get('site_name');
                $this->view->menu = 'system-news';
                $this->view->view('author');
                return;
            }
        }

        $err = new Error();
        $err->error(404);
    }


    public function test()
    {

        $this->view->view('test');
    }
}