<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 23.11.15
 * Time: 11:03
 */
class Points extends BaseController
{
    public function index()
    {
        $this->view->tasks = DB::run()->query('select * from tasks where user_id = ' . Registry::get('user')->id)->fetchAll();
        $this->view->pageDescription = 'Отображение состояния баллов в системе';
        $this->view->pageTitle = 'Баллы - ' . Registry::get('site_name');
        $this->view->menu = 'points';
        $this->view->view('index');
    }

    public function add()
    {
        $taskId = (int) $this->getParam('task', 0);

        if (empty($taskId)) exit(json_encode(['result' => 'fail', 'message' => 'Неопределённая задача!']));

        $stmt = DB::run()->prepare('insert into points (user_id, task_id, value, date) values (?, ?, ?, ?)');

        try {
            DB::run()->beginTransaction();
            $value = DB::run()->query('select value from tasks where id = ' . $taskId)->fetchColumn();
            $stmt->execute([Registry::get('user')->id, $taskId, $value, date('Y-m-d H:i:s')]);
            DB::run()->commit();
            exit(json_encode(['result' => 'done', 'message' => 'Баллы успешно добавлены!']));
        } catch (PDOException $e) {
            DB::run()->rollBack();
            exit(json_encode(['result' => 'fail', 'message' => $e->getMessage()]));
        }
    }

    public function get()
    {
        $period = date('Y-m-d', strtotime('-' . Registry::get('days_with_points') . ' days'));

        $points = DB::run()->query('select p.hold_reason, p.value, p.date, t.name task_name from points p
            left join tasks t on t.id = p.task_id
            left join users u on u.id = p.user_id
            where p.user_id = ' . Registry::get('user')->id .
            ' and p.date > ' . DB::run()->quote($period . ' 23:59:59') .
            ' order by p.date asc')->fetchAll();

        if (empty($points)) exit(json_encode(['result' => 'fail', 'message' => 'Нет данных для отображения']));

        exit(json_encode(['result' => 'done', 'points' => $points]));
    }

    public function getMyPoints()
    {
        $sum = DB::run()->query('select sum(value) from points where user_id = ' . Registry::get('user')->id)->fetchColumn();
        exit(json_encode(['sum' => ($sum ?: 0)]));
    }

    public function getPointsById($id)
    {
        return DB::run()->query('select sum(value) from points where user_id = ' . $id)->fetchColumn();
    }

    public function hold()
    {
        $userId = (int) $this->getParam('user_id');

        if ($userId) {
            $isId = DB::run()->query('select id from users where id = ' . $userId . ' and family = ' . Registry::get('user')->family . ' limit 1')->fetch();
            if (!$isId) exit(json_encode(['result' => 'fail', 'message' => 'Пользователь не находится в данной семье']));
        } else $userId = Registry::get('user')->id;

        $reason = $this->getParam('task_name');
        $value = (int) $this->getParam('task_value', 0);
        $value = $value == 0 ? Registry::get('min_task_value') :
            ($value < Registry::get('min_task_value') ? Registry::get('min_task_value') :
                ($value > Registry::get('max_task_value') ? Registry::get('max_task_value') : $value)
            );

        if (empty($reason)) {
            exit(json_encode(['result' => 'fail', 'message' => 'Поле &quot;Причина удержания баллов&quot; должно быть заполнено!']));
        }

        $stmt = DB::run()->prepare('insert into points (user_id, task_id, hold_reason, value, date) values (?, ?, ?, ?, ?)');

        try {
            DB::run()->beginTransaction();
            $stmt->execute([$userId, 0, Tools::ucfirst($reason), -$value, date('Y-m-d H:i:s')]);
            DB::run()->commit();
            exit(json_encode(['result' => 'done', 'message' => 'Снятие баллов произведено успешно!', 'type' => 'hold-points']));
        } catch (PDOException $e) {
            DB::run()->rollBack();
            exit(json_encode(['result' => 'fail', 'message' => $e->getMessage()]));
        }
    }
}