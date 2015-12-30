<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 08.11.15
 * Time: 01:42
 */
class Auth extends BaseController
{
    public function __construct()
    {
        $this->args = func_get_args();
        $this->view = new View($this->layout, get_class($this));
    }

    public function index()
    {
        Tools::redirect();
    }

    public function login()
    {
        $email = strtolower($this->getParam('email'));
        $password = $this->getParam('password');
        $remember = $this->getParam('remember');

        if (!$email || !$password) {
            exit(json_encode(['result' => 'fail', 'message' => 'Все поля формы обязательны для заполнения']));
        }

//        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//            exit(json_encode(['result' => 'fail', 'message' => 'Некорректный Email-адрес']));
//        }

        if (iconv_strlen($password) < Registry::get('min_password_length') || iconv_strlen($password) > Registry::get('max_password_length')) {
            exit(json_encode(['result' => 'fail', 'message' => 'Длина пароля должна быть в пределах от ' .
                Registry::get('min_password_length') . ' до ' .
                Registry::get('max_password_length') . ' символов']));
        }

        $user = DB::run()->query("select * from users where email = '$email'")->fetch();

        if (!$user) {
            exit(json_encode(['result' => 'fail', 'message' => 'Данный Email-адрес в системе не зарегистрирован']));
        }

        $userPassHash = Tools::hash($password, Registry::get('hash_salt'));

        if ($user->password != $userPassHash) {
            exit(json_encode(['result' => 'fail', 'message' => 'Неверный пароль']));
        }

        if ($remember === 'true') Tools::setUserAuth($user, true);
        else Tools::setUserAuth($user);

        exit(json_encode(['result' => 'done', 'message' => 'Успешный вход в систему!<br>Сейчас Вы будете перенаправлены.']));
    }

    public function logout()
    {
        Tools::cleanUserAuth();
    }

    public function registration()
    {
        $mode = $this->getParam('mode');
        $email = strtolower($this->getParam('email'));
        $name = Tools::ucwords($this->getParam('name', 'NoName'));
        $familyName = Tools::ucwords($this->getParam('family_name'));
        $password = $this->getParam('password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            exit(json_encode(['result' => 'fail', 'message' => 'Некорректный Email-адрес']));
        }

        if ($mode == 'check-email') {
            $email = DB::run()->query("select email from users where email = '" . $email . "'")->fetch();
            if ($email) exit(json_encode(['result' => 'fail', 'message' => 'Такой Email уже зарегистрирован в системе!']));
            else exit(json_encode(['result' => 'done', 'message' => 'Email свободен для регистрации']));
        }

        if (!$email || !$password) exit(json_encode(['result' => 'fail', 'message' => 'Все поля формы обязательны для заполнения']));

        $family = DB::run()->prepare('insert into families (name) values (?)');
        $user = DB::run()->prepare('insert into users (family, name, email, password, role, registered_date, updated_at, remember_token) values (?, ?, ?, ?, ?, ?, ?, ?)');
        $rememberToken = Tools::hash($email, time());

        try {
            DB::run()->beginTransaction();
            $family->execute([$familyName]);
            $familyId = DB::run()->lastInsertId('id');
            $registeredDate = date('Y-m-d H:i:s');
            $user->execute([$familyId, $name, $email, Tools::hash($password, Registry::get('hash_salt')), 'manager', $registeredDate, $registeredDate, $rememberToken]);
            $userId = DB::run()->lastInsertId('id');
            DB::run()->commit();
        } catch (PDOException $e) {
            DB::run()->rollBack();
            exit(json_encode(['result' => 'fail', 'message' => $e->getMessage()]));
        }

        if ($userId) {
            setcookie('auth', $rememberToken, time()+3600*24*2, '/');
            Tools::setUserAuth((object) ['id' => $userId, 'family' => $familyId, 'name' => $name, 'email' => $email, 'role' => 'manager', 'registered_date' => $registeredDate]);
        }

        exit(json_encode(['result' => 'done', 'message' => 'Успешная регистрация!<br>Сейчас Вы будете перенаправлены!']));
    }

    private function loginationById()
    {
        //
    }
}