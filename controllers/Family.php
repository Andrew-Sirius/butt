<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 02.12.15
 * Time: 12:05
 */
class Family extends BaseController
{
    public function index()
    {
        if (!Registry::get('is_manager')) {
            $err = new Error();
            $err->error(404);
            return;
        }

        $period = date('Y-m-d', strtotime('-' . Registry::get('days_with_points') . ' days'));
        $family = DB::run()->query('select u.id, u.name, t.name task_name, t.daily, t.value from users u
            left join tasks t on t.user_id=u.id
            where u.family = ' . Registry::get('user')->family . ' and u.id != ' . Registry::get('user')->id . '
            order by u.id asc, t.value desc')->fetchAll();
        if ($family) {
            foreach ($family as $key => $user) {
                $fam[$user->id]['name'] = $user->name;
                $fam[$user->id]['total_points'] = DB::run()->query('select sum(value) from points where user_id = ' . $user->id)->fetchColumn();
                $fam[$user->id]['tasks'][$key]['name'] = $user->task_name;
                $fam[$user->id]['tasks'][$key]['value'] = $user->value;
                $fam[$user->id]['tasks'][$key]['daily'] = $user->daily;
            }
            $this->view->family = $fam;
        } else $this->view->family = false;

        $this->view->familyPoints = DB::run()->query('select u.id, t.name task_name, p.hold_reason, p.value, p.date from users u
            left join points p on p.user_id=u.id
            left join tasks t on t.id=p.task_id
            where u.family = ' . Registry::get('user')->family . '
            and u.id != ' . Registry::get('user')->id . '
            and p.date > ' . DB::run()->quote($period . ' 23:59:59') . '
            order by u.id')->fetchAll();
        $this->view->pageDescription = 'Данный раздел предназначен для просмотра информации по баллам о своей семье';
        $this->view->pageTitle = 'Моя семья - ' . Registry::get('site_name');
        $this->view->menu = 'family';
        $this->view->view('index');
    }
}