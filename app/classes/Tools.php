<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 07.11.15
 * Time: 11:34
 */
final class Tools
{
    public static function hash($str, $salt = '')
    {
        return hash('whirlpool', $salt . $str . $salt);
    }

    public static function url($str = '', $abs = true)
    {
        $uri = str_replace('index.php', '', str_replace(SITEPATH . 'public_html', '', $_SERVER['SCRIPT_FILENAME'])) . trim($str, '/');

        if ($abs)
            return 'http://' . $_SERVER['SERVER_NAME'] . $uri;
        else
            return $uri;
    }

    public static function redirect($uri = '')
    {
        header('Location: ' . self::url($uri));
        exit;
    }

    static function encryptData($key, $text)
    {
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));
    }

    static function decryptData($key, $text)
    {
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)), '\0');
    }

    static public function setUserAuth($user, $remember = false)
    {
        $_SESSION['user']['id'] = (int) $user->id;
        $_SESSION['user']['family'] = (int) $user->family;
        $_SESSION['user']['name'] = empty($user->name) ? 'NoName' : $user->name;
        $_SESSION['user']['email'] = $user->email;
        $_SESSION['user']['role'] = $user->role;
        $_SESSION['user']['registered_date'] = $user->registered_date;
        $_SESSION['user']['ip'] = $_SERVER['REMOTE_ADDR'];
        Registry::set('user', (object) $_SESSION['user']);

        // TODO при расширении системы в апдейтах установить лимиты
        DB::run()->query('update users set last_login = ' .
            DB::run()->quote(date('Y-m-d H:i:s')) .
            ' where email = ' . DB::run()->quote($user->email));

        if ($remember) {
            $rememberToken = self::hash($user->email);
            setcookie('auth', $rememberToken, time()+3600*24*2, '/');
            DB::run()->query("update users set remember_token = '" . $rememberToken . "' where email = '" . $user->email . "'");
        }
    }

    static public function cleanUserAuth()
    {
        if (empty(Registry::get('user')->email)) self::redirect();

        DB::run()->query("update users set remember_token = null where email = '" . Registry::get('user')->email . "'");
        session_destroy();
        unset ($_SESSION['user']);
        self::redirect();
    }

    static public function ucfirst($string, $e ='utf-8')
    {
        $string = trim($string);
        if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) {
            $string = mb_strtolower($string, $e);
            $upper = mb_strtoupper($string, $e);
            preg_match('#(.)#us', $upper, $matches);
            $string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $e), $e);
        } else {
            $string = ucfirst($string);
        }
        return $string;
    }

    static public function ucwords($string, $e ='utf-8')
    {
        $string = trim($string);
        if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) {
            foreach (explode(' ', $string) as $word) {
                $str = mb_strtolower($word, $e);
                $upper = mb_strtoupper($str, $e);
                preg_match('#(.)#us', $upper, $matches);
                $res[] = $matches[1] . mb_substr($str, 1, mb_strlen($str, $e), $e);
            }
            $string = implode(' ', $res);
        } else {
            $string = ucwords($string);
        }
        return $string;
    }

    static public function logToFile($file, $data, $flags = FILE_APPEND)
    {
        $logFile = SITEPATH . 'log' . DS . $file . '.log';
        file_put_contents($logFile, $data, $flags);
    }
}