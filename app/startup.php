<?
function __autoload($class_name) {

    $file = SITEPATH . 'app' . DS . 'classes' . DS . $class_name . '.php';

    if (!file_exists($file)) return;

    require_once $file;
}

session_start();

if (($handle = fopen(SITEPATH . 'app' . DS . 'config.csv', 'r')) !== false) {
    while (($data = fgetcsv($handle, 500)) !== false) {
        Registry::set($data[0], $data[1]);
    }
    fclose($handle);
}

Registry::set('user', empty($_SESSION['user']) ? null : (object) $_SESSION['user']);

$rememberToken = empty($_COOKIE['auth']) ? null : $_COOKIE['auth'];
if ($rememberToken && empty(Registry::get('user'))) {
    $user = DB::run()->query("select * from users where remember_token = '" . $rememberToken . "'")->fetch();
    if ($user) {
        Tools::setUserAuth($user, true);
    } else setcookie('auth', '', 0);
}

if (!empty(Registry::get('user'))) {
    if (Registry::get('user')->role == 'manager' || Registry::get('user')->role == 'admin')
        Registry::set('is_manager', true);
    else Registry::set('is_manager', false);
}