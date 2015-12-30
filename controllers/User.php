<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 18.11.15
 * Time: 23:41
 */
class User extends BaseController
{
    public function index()
    {
        $this->view->user = DB::run()->query('select u.name, f.name family_name from users u left join families f on f.id=u.family where u.id = ' . Registry::get('user')->id)->fetch();
        $this->view->family = DB::run()->query('select *, (select sum(value) from points where user_id = users.id) points from users where family = ' . Registry::get('user')->family . ' order by id')->fetchAll();
        $this->view->pageDescription = 'Личный кабинет пользователя';
        $this->view->pageTitle = 'Личный кабинет - ' . Registry::get('site_name');
        $this->view->menu = 'private-cabinet';
        $this->view->section = $this->getParam('section', 'users');
        $this->view->view('index');
    }

    public function changeProfile()
    {
        $name = Tools::ucwords($this->getParam('user_name'));
        $familyName = $this->getParam('family_name');
        $password = $this->getParam('user_password');
        $getChangePassword = $this->getParam('get_change_password');

        $query = 'update users set';

        if (!empty($name) && $name != Registry::get('user')->name) {
            $query .= ' name = ?';
            $values[] = $name;
        }

        if ($getChangePassword == 'true' && $password) {
            if (iconv_strlen($password) < Registry::get('min_password_length') || iconv_strlen($password) > Registry::get('max_password_length')) {
                exit(json_encode(['result' => 'fail', 'message' => 'Длина пароля должна быть в пределах от ' .
                    Registry::get('min_password_length') . ' до ' .
                    Registry::get('max_password_length') . ' символов']));
            }
            if ($query != 'update users set') $query .= ',';
            $query .= ' password = ?';
            $values[] = Tools::hash($password, Registry::get('hash_salt'));
        }

        if(!empty($familyName)) {
            $stmt = DB::run()->prepare('update families set name = ? where id = ?');
            try {
                DB::run()->beginTransaction();
                $stmt->execute([$familyName, Registry::get('user')->family]);
                DB::run()->commit();
                $res = true;
            } catch (PDOException $e) {
                DB::run()->rollBack();
                exit(json_encode(['result' => 'fail', 'message' => $e->getMessage()]));
            }
        }

        if ($query != 'update users set') {
            $query .= ', updated_at = ? where id = ?';
            $values[] = date('Y-m-d H:i:s');
            $values[] = Registry::get('user')->id;
            $stmt = DB::run()->prepare($query);
            try {
                DB::run()->beginTransaction();
                $stmt->execute($values);
                DB::run()->commit();
                $_SESSION['user']['name'] = $name;
                $res = true;
            } catch (PDOException $e) {
                DB::run()->rollBack();
                exit(json_encode(['result' => 'fail', 'message' => $e->getMessage()]));
            }
        }

        if(!empty($res)) exit(json_encode(['result' => 'done', 'message' => 'Изменения успешно приняты!', 'name' => $name]));
        else exit(json_encode(['result' => 'fail', 'message' => 'Нечего изменять!']));
    }

    public function add()
    {
        $email = strtolower($this->getParam('email'));
        $name = Tools::ucwords($this->getParam('name'));
        $password = $this->getParam('password');

        if (!empty($email) && !empty($name) && !empty($password)) {
            $registeredEmail = DB::run()->query('select email from users where email = ' . DB::run()->quote($email))->fetch();
            if ($registeredEmail) exit(json_encode(['result' => 'fail', 'message' => 'Такой Email/Логин уже зарегистрирован в системе!']));

            $stmt = DB::run()->prepare('insert into users (family, name, email, password, role, registered_date, updated_at) values (?, ?, ?, ?, ?, ?, ?)');
            $user = [
                Registry::get('user')->family,
                $name,
                $email,
                Tools::hash($password, Registry::get('hash_salt')),
                'user',
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s'),
            ];

            try {
                DB::run()->beginTransaction();
                $stmt->execute($user);
                DB::run()->commit();
                exit(json_encode(['result' => 'done', 'message' => 'Новый пользователь успешно добавлен в систему!', 'user' => $user]));
            } catch (PDOException $e) {
                DB::run()->rollBack();
                exit(json_encode(['result' => 'fail', 'message' => $e->getMessage()]));
            }
        }

        exit(json_encode(['result' => 'fail', 'message' => 'Все поля должны быть заполнены!']));
    }

    public function addFamily()
    {
        if (Registry::get('user')->role != 'admin') {
            $err = new Error();
            $err->error(404);
            return;
        }

        $name = $this->getParam('name');
        $email = strtolower($this->getParam('email'));
        $password = $this->getParam('password');
        $familyName = $this->getParam('family');

        if (!empty($name) && !empty($email) && !empty($password) && !empty($familyName)){
            $registeredEmail = DB::run()->query('select email from users where email = ' . DB::run()->quote($email))->fetch();
            if ($registeredEmail) exit(json_encode(['result' => 'fail', 'message' => 'Такой Email/Логин уже зарегистрирован в системе!']));

            $stmt = DB::run()->prepare('insert into users (family, name, email, password, role, registered_date, updated_at) values (?, ?, ?, ?, ?, ?, ?)');
            $stmt2 = DB::run()->prepare('insert into families (name) values (?)');

            try {
                DB::run()->beginTransaction();
                $date = date('Y-m-d H:i:s');
                $stmt2->execute([$familyName]);
                $familyId = DB::run()->lastInsertId('id');
                $stmt->execute([$familyId, $name, $email, Tools::hash($password, Registry::get('hash_salt')), 'manager', $date, $date]);
                DB::run()->commit();
                exit(json_encode(['result' => 'done', 'message' => 'Новый менеджер и семья успешно добавлены в систему!']));
            } catch (PDOException $e) {
                DB::run()->rollBack();
                exit(json_encode(['result' => 'fail', 'message' => $e->getMessage()]));
            }
        }

        exit(json_encode(['result' => 'fail', 'message' => 'Все поля должны быть заполнены!']));
    }
}