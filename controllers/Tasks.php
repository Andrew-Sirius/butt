<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 24.11.15
 * Time: 13:49
 */
class Tasks extends BaseController
{
    public function index(){}

    public function get()
    {
        $email = $this->getParam('email');

        if (empty($email)) $id = Registry::get('user')->id;
        else $id = (int) DB::run()->query('select id from users where email = ' . DB::run()->quote($email) . ' and family = ' . Registry::get('user')->family)->fetchColumn();

        $tasks = DB::run()->query('select * from tasks where user_id = ' . $id . ' order by value desc')->fetchAll();

        if (empty($tasks)) exit(json_encode(['result' => 'fail', 'message' => 'Ещё нет задач в бонусной таблице']));

        exit(json_encode(['result' => 'done', 'tasks' => $tasks]));
    }

    public function add() {
        $taskName = Tools::ucfirst($this->getParam('task_name'));
        $taskValue = (int) $this->getParam('task_value', 0);
        $taskValue = $taskValue == 0 ? Registry::get('min_task_value') :
            ($taskValue < Registry::get('min_task_value') ? Registry::get('min_task_value') :
                ($taskValue > Registry::get('max_task_value') ? Registry::get('max_task_value') : $taskValue)
            );

        if (empty($taskName)) {
            exit(json_encode(['result' => 'fail', 'message' => 'Поле &quot;Название задачи&quot; должно быть заполнено!']));
        }

        $email = $this->getParam('email');

        if (empty($email)) $id = Registry::get('user')->id;
        else $id = DB::run()->query('select id from users where email = ' . DB::run()->quote($email) . ' and family = ' . Registry::get('user')->family)->fetchColumn();

        $resp = DB::run()->query('select name from tasks where user_id = ' . $id . ' and name = ' . DB::run()->quote($taskName))->fetchColumn();

        if ($resp) {
            exit(json_encode(['result' => 'fail', 'message' => 'Задача с таким названием уже существует!']));
        }

        $stmt = DB::run()->prepare('insert into tasks (user_id, family_id, name, value) values (?, ?, ?, ?)');

        try {
            DB::run()->beginTransaction();
            $stmt->execute([$id, Registry::get('user')->family, $taskName, $taskValue]);
            DB::run()->commit();
            exit(json_encode(['result' => 'done', 'message' => 'Новая задача успешно добавлена!', 'type' => 'add-task']));
        } catch (PDOException $e) {
            DB::run()->rollBack();
            exit(json_encode(['result' => 'fail', 'message' => $e->getMessage()]));
        }
    }

    public function getTaskById()
    {
        $taskId = $this->getParam('task_id');

        $stmt = DB::run()->prepare('select * from tasks where family_id = :fid and id = :id limit 1');

        try {
            $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
            $stmt->bindParam(':fid', Registry::get('user')->family, PDO::PARAM_INT);
            $stmt->execute();
            $task = $stmt->fetch();
            exit(json_encode([
                'result' => 'done',
                'id' => $task->id,
                'name' => $task->name,
                'daily' => $task->daily,
                'value' => $task->value,
            ]));
        } catch (PDOException $e) {
            exit(json_encode(['result' => 'fail', 'message' => $e->getMessage()]));
        }
    }

    public function edit()
    {
        $id = $this->getParam('id');
        $name = $this->getParam('name');
        $value = $this->getParam('value');

        $value = $value == 0 ? Registry::get('min_task_value') :
            ($value < Registry::get('min_task_value') ? Registry::get('min_task_value') :
                ($value > Registry::get('max_task_value') ? Registry::get('max_task_value') : $value)
            );

        $daily = $this->getParam('daily') == 'true' ? 1 : 0;

        $stmt = DB::run()->prepare('update tasks set name = :n, value = :v, daily = :d where id = :id and family_id = :fid');

        try {
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fid', Registry::get('user')->family, PDO::PARAM_INT);
            $stmt->bindParam(':n', $name);
            $stmt->bindParam(':v', $value, PDO::PARAM_INT);
            $stmt->bindParam(':d', $daily, PDO::PARAM_INT);
            $stmt->execute();
            exit(json_encode(['result' => 'done', 'message' => 'Задача успешно обновлена!']));
        } catch (PDOException $e) {
            exit(json_encode(['result' => 'fail', 'message' => $e->getMessage()]));
        }
    }
}