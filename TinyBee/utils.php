<?php

if (!function_exists('randomPassword')) {
    function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $token = "";
        $max = strlen($alphabet) - 1;
        for ($i = 0; $i < 32; $i++) {
            $token .= $alphabet[random_int(0, $max)];
        }
        return $token;
    }
}

if (!function_exists('filterData')) {
    function filterData($data, $type)
    {
        if ($type == "mail") {
            return (filter_var($data, FILTER_VALIDATE_EMAIL));
        }
        if ($type == "name" || $type == "password") {
            return (filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        }
    }
}
// 100.
// Lien vers la ressource cible de l'image, Lien vers la ressource source de l'image, coordonnée du point de destination, coordonnée du point de destination
if (!function_exists('imagecopymerge_alpha')) {
    function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);

        // copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

        // copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

        // insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    }
}

if (!function_exists('keysExist')) {
    function keysExist($required, $data)
    {
        if (!isset($data) || empty($data)) {
            return (false);
        }
        if (in_array(null, $data, true) || in_array('', $data, true)) {
            return (false);
        }
        return (count(array_intersect_key(array_flip($required), $data)) === count($required));
    }
}


if (!function_exists('sendHtmlMail')) {
    function sendHtmlMail($to, $name, $content, $subject)
    {
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';

        $headers[] = 'To: '.$name.' <'.$to.'>';
        $headers[] = 'From: Matcha <no-reply@matcha.fr>';

        $message = "
        <html>
        <head>
        <title>Mail confirmation</title>
        </head>
        <body>";
        $message.= $content;
        $message.= "</body></html>";
        $success = mail($to, $subject, $message, implode("\r\n", $headers));
        if (!$success) {
            //$errorMessage = error_get_last()['message'];
        }
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        header("location: " . $url);
    }
}

if (!function_exists('isValidRegex')) {
    function isValidRegex($pattern, $string)
    {
        return (preg_match($pattern, $string));
    }
}

if (!function_exists('view')) {
    function view($path, $data = null)
    {
        if ($data !== null) {
            extract($data, EXTR_SKIP);
        }
        require_once(dirname(__DIR__).'/views/'.$path);
        die();
    }
}

if (!function_exists('isAuth')) {
    function isAuth()
    {
        if (!isset($_SESSION)) {
            return (0);
        }

        if (!keysExist(['user_id', 'token'], $_SESSION)) {
            return (0);
        }
        $db = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT password, is_reset, is_confirmed FROM User WHERE id=?";
        $prepare = $db->prepare($sql);
        $prepare->execute([$_SESSION['user_id']]);
        $result = $prepare->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return (0);
        }
        if (hash_equals($result['password'], $_SESSION['token'])) {
            if ($result['is_confirmed'] === '1' && ($result['is_reset'] === '0' || $result['is_reset'] === '1')) {
                return (1);
            }
        }
        return (0);
    }
}

if (!function_exists('setLastVisited')) {
    function setLastVisited()
    {
        date_default_timezone_set('Europe/Paris');
        $db = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE User SET last_visited = ? WHERE id = ?";
        $prepare = $db->prepare($sql);
        $currentTime = date("Y-m-d H:i:s");
        $prepare->execute([$currentTime, $_SESSION['user_id']]);
    }
}

if (!function_exists('checkBase64Format')) {
    function checkBase64Format($image)
    {
        $explode = explode(',', $image);
        $allow = ['png', 'jpg', 'jpeg'];
        $format = str_replace(
            [
                'data:image/',
                ';',
                'base64',
            ],
            [
                '', '', '',
            ],
            $explode[0]
        );
        // check file format
        if (!in_array($format, $allow)) {
            return false;
        }
        // check base64 format
        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
            return false;
        }
        return true;
    }
}

if (!function_exists('isXmlHttpRequest')) {
    function isXmlHttpRequest()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
    }
}

if (!function_exists('base64ToImage')) {
    function base64ToImage($base64_string, $path)
    {
        $ifp = fopen($path, 'wb');
        $data = explode(',', $base64_string);
        $str = str_replace(' ', '+', trim($data[1]));
        fwrite($ifp, base64_decode($str));
        fclose($ifp);
        chmod($path, 0777);
        return $path;
    }
}

if (!function_exists('calc_pagination')) {
    function calc_pagination($count, $itemsNumber)
    {
        $result = 1;
        if ($count > 0) {
            $pagination = $count / $itemsNumber;
            $result = (is_float($pagination) ? 1 : 0) + (int)$pagination;
        }
        return ($result);
    }
}

if (!function_exists('createClassArray')) {
    function createClassArray($path)
    {
        $arrayClass = [];
        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isFile()) {
                $fullName = $file->getFilename();
                $name = strtolower(str_replace(['.php', ucfirst($path)], '', $fullName));
                $className = str_replace('.php', '', $fullName);
                if (!array_key_exists($name, $arrayClass)) {
                    require_once($file->getRealPath());
                    $arrayClass[$name] = new $className();
                }
            }
        }
        return ($arrayClass);
    }
}
if (!function_exists('extract_base64')) {
    function extract_base64($base64)
    {
        $data = explode(',', $base64);
        $str = str_replace(' ', '+', trim($data[1]));
        return (base64_decode($str));
    }
}

if (!function_exists('loadSession')) {
    function loadSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
}

if (!function_exists('implodeToPdo')) {
    function implodeToPdo($glueKV, $gluePair, $KVarray, $isInsert = false)
    {
        $tmp = array();
        foreach ($KVarray as $key => $val) {
            if ($isInsert) {
                $tmp[] = $glueKV;
            } else {
                $tmp[] = $key . $glueKV . '?';
            }
        }
        return implode($gluePair, $tmp);
    }
}

if (!function_exists('execQuery')) {
    function execQuery($db, $sql, $data, $option = false, $fetchMethod = false)
    {
        $prepare = $db->prepare($sql);
        try {
            !empty($data) && is_array($data) ? $prepare->execute($data) : $prepare->execute();
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die();
        }

        if ($fetchMethod !== false && ($fetchMethod == (FETCH_ONE || FETCH_ALL)) && isset($option)) {
            return ($fetchMethod === FETCH_ALL ? $prepare->fetchAll($option) : $prepare->fetch($option));
        }
    }
}

if (!function_exists('encodeToJs')) {
    function encodeToJs($data)
    {
        return (json_encode($data, JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS));
    }
}

if (!function_exists('group_by')) {
    function group_by($key, $array)
    {
        $result = array();

        foreach ($array as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }
}

if (!function_exists('getIp')) {
    function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        return ($ip);
    }
}

if (!function_exists('getRequestHeaders')) {
    function getRequestHeaders()
    {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }
}

if (!function_exists('validateDate')) {
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

if (!function_exists('geoCoordsDistance')) {
    function geoCoordsDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $rad = M_PI / 180;
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad)
            * sin($latitudeTo * $rad) + cos($latitudeFrom * $rad)
            * cos($latitudeTo * $rad) * cos($theta * $rad);
        return acos($dist) / $rad * 60 *  1.853;
    }
}

if (!(function_exists('computeScore'))) {
    function computeScore()
    {
        $db = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT last_visited FROM User WHERE User.id = ?";
        $result = execQuery($db, $sql, [$_SESSION['user_id']], PDO::FETCH_ASSOC, FETCH_ONE);
        $days = 0;
        $likes = 0;
        $block = 0;
        $visited = 0;
        if (isset($result['last_visited'])) {
            $date = new DateTime($result['last_visited']);
            $now = new DateTime();
            $diff = (int)$date->diff($now)->format("%d");
            $days = $diff < 100 ? (100 - $date->diff($now)->format("%d")) * 0.01 : 0;
        }
        $sql = "SELECT count(liked_by) AS 'like_counter' FROM Likes WHERE Likes.user_id = ?";
        $result = execQuery($db, $sql, [$_SESSION['user_id']], PDO::FETCH_ASSOC, FETCH_ONE);
        if ($result && isset($result['like_counter'])) {
            $likes = $result['like_counter'] * 4;
        }
        $sql = "SELECT count(blocked_user) AS 'block_counter' FROM Blocked WHERE Blocked.user_id = ?";
        $result = execQuery($db, $sql, [$_SESSION['user_id']], PDO::FETCH_ASSOC, FETCH_ONE);
        if ($result && isset($result['block_counter'])) {
            $block = $result['block_counter'] * 2;
        }
        $sql = "SELECT count(visiter_id) AS 'visiter_counter' FROM Visite WHERE Visite.user_id = ?";
        $result = execQuery($db, $sql, [$_SESSION['user_id']], PDO::FETCH_ASSOC, FETCH_ONE);
        if ($result && isset($result['visiter_counter'])) {
            $visited = $result['visiter_counter'] * 2;
        }
        $val = ($likes + $visited + $days) - $block;
        $score = $val > 0 ? (100 * ($val / 650)) : 0;
        $score = $score > 100 ? 100 : $score;
        $sql = "UPDATE Profil SET score = ? WHERE Profil.user_id = ?";
        execQuery($db, $sql, [round($score), $_SESSION['user_id']]);
    }
}

if (!function_exists('oneDimArray')) {
    function oneDimArray($array)
    {
        return iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator(
            $array
      )), 0);
    }
}

if (!function_exists('normalize')) {
    function normalize($val, $min, $max)
    {
        return ($val - $min) / ($max - $min);
    }
}

if (!function_exists('echoJsAndDie')) {
    function outputJsError($data)
    {
        echo encodeToJs(['error' => $data]);
        die();
    }
}
