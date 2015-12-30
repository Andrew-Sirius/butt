<?php

/**
 * Created by PhpStorm.
 * User: andrew (and.sirius@gmail.com)
 * Date: 25.12.15
 * Time: 17:43
 */
class Cron extends BaseController
{
    private $lockFile = SITEPATH . 'log' . DS . 'cron_lock';
    private $validKey = 'Kh75D_ndh6Fs7B27dfGhTy_ieu7010';
    private $fromDate;
    private $toDate;

    public function __construct($args)
    {
        $this->noAuth = true;
        parent::__construct($args);

        $day = strtotime('-1 days');
        $this->fromDate = date('Y-m-d 00:00:00', $day);
        $this->toDate = date('Y-m-d 23:59:59', $day);

        if ($this->getParam('valid_key') != $this->validKey) {
            Tools::logToFile('system', date('Y-m-d H:i:s') . "\r\n" . 'Несанкционированный вызов планировщика' . "\r\n\r\n\r\n");
            exit;
        }
    }

    public function index() {}

    public function daily_tasks()
    {
        touch($this->lockFile);
        $log = [];

        $users = DB::run()->query('select id from users order by id')->fetchAll();

        $stmt = DB::run()->prepare('select id from points where user_id = :uid and task_id = :tid and date >= :from and date <= :to');
        $stmt->bindParam(':from', $this->fromDate);
        $stmt->bindParam(':to', $this->toDate);
        $log[] = __METHOD__ . ' - Start | ' . date('Y-m-d H:i:s') . "\r\n\r\n";
        $log[] = 'Period: ' . $this->fromDate . ' - ' . $this->toDate . "\r\n\r\n";
        foreach ($users as $user) {
            $dailyTasks = DB::run()->query('select * from tasks where daily = 1 and user_id = ' . $user->id)->fetchAll();
            $stmt->bindParam(':uid', $user->id, PDO::PARAM_INT);
            $log[] = '    User id: ' . $user->id . ' - ';
            $incompleteDailyTasks = 0;
            foreach ($dailyTasks as $task) {
                $stmt->bindParam(':tid', $task->id, PDO::PARAM_INT);
                $stmt->execute();
                if (!$stmt->fetch()) {
                    $this->insertToPoints($user->id, 'Не выполнена ежедневная задача: "' . $task->name . '"', $task->value, $log);
                    $incompleteDailyTasks++;
                }
            }
            $log[] = 'Incomplete tasks count: ' . $incompleteDailyTasks . "\r\n";
            sleep(1);
        }

        $log[] = "\r\n" . __METHOD__ . ' - Complete! | ' . date('Y-m-d H:i:s') . "\r\n=====\r\n\r\n";
        Tools::logToFile('daily_tasks', $log);
        unlink($this->lockFile);
        sleep(10);
        $this->daily_minimum_points();
    }

    public function daily_minimum_points()
    {
        touch($this->lockFile);
        $log = [];

        $users = DB::run()->query('select id, daily_points from users order by id')->fetchAll();
        $stmt = DB::run()->prepare('select sum(value) from points where user_id = :uid and date >= :from and date <= :to');
        $stmt->bindParam(':from', $this->fromDate);
        $stmt->bindParam(':to', $this->toDate);
        $log[] = __METHOD__ . ' - Start | ' . date('Y-m-d H:i:s') . "\r\n\r\n";
        $log[] = 'Period: ' . $this->fromDate . ' - ' . $this->toDate . "\r\n\r\n";
        foreach ($users as $user) {
            $stmt->bindParam(':uid', $user->id, PDO::PARAM_INT);
            $log[] = '    User id: ' . $user->id . ' - ';
            $stmt->execute();
            $dailyPoints = (int) $stmt->fetchColumn();
            if ($dailyPoints < (int) $user->daily_points && $dailyPoints > 0) {
                $this->insertToPoints($user->id, 'Не набрано минимальное количество баллов (' . $user->daily_points . ') за день', $dailyPoints, $log);
                $log[] = 'No collect daily points (' . $dailyPoints . ' from ' . $user->daily_points . ')' . "\r\n";
            } else {
                $log[] = 'Collected points: ' . $dailyPoints . '. Minimum daily: ' . $user->daily_points . "\r\n";
            }
            sleep(1);
        }

        $log[] = "\r\n" . __METHOD__ . ' - Complete! | ' . date('Y-m-d H:i:s') . "\r\n=====\r\n\r\n";
        Tools::logToFile('daily_minimum_points', $log);
        unlink($this->lockFile);
    }

    private function insertToPoints($userId, $reason, $value, &$log)
    {
        $stmt = DB::run()->prepare('insert into points (user_id, task_id, hold_reason, value, date) values(:uid, 0, :reason, -:value, :date)');

        try {
            $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':reason', $reason);
            $stmt->bindParam(':value', $value, PDO::PARAM_INT);
            $stmt->bindParam(':date', $this->toDate);
            $stmt->execute();
        } catch (PDOException $e) {
            $log[] = $e->getMessage();
        }
    }
}