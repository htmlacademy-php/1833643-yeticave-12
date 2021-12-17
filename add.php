<?php

$con = mysqli_connect("localhost", "root", "root", "yeticave");
require_once('helpers.php');
$required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["submit"])) {
    error_reporting(E_ALL);

    $isAuth = rand(0, 1);
    $userName = 'IgorGrinev'; // укажите здесь ваше имя
    $title = 'Добавление лота';

    ini_set('display_errors', 'on');
    foreach ($required_fields as $field) {
        if (readPOST($field) == null) {
            $errors[$field] = 'Поле не заполнено';
        }
    }
    $name = readPOST('lot-name');
    $category = (int)readPOST('category');
    $message = readPOST('message');
    $lot_rate = (int)readPOST('lot-rate');
    $lot_step = (int)readPOST('lot-step');
    $lot_date = readPOST('lot-date');
    if (null !== $name) {
        if (strlen($name) <= 0 || strlen($name) > 10) {
            $errors['lot-name'] = 'Длина имени лота должна быть от 0 до 10 символов';
        }
    }
    if (null !== $lot_date) {
        if (false === is_date_valid($lot_date)) {
            $errors['lot-date'] = 'Неверный формат даты';
        } else {
            $time_now = strtotime("now");
            $experation_stamp = strtotime($lot_date);
            $diff_time = $experation_stamp - $time_now;
            $day = 86400;
            if ($diff_time < $day) {
                $errors['lot-date'] = 'Указанная дата должна быть больше текущей даты, хотя бы на один день';
            }
        }
    }
    if (null !== $lot_rate) {
        if (!is_int($lot_rate) || $lot_rate <= 0) {
            $errors['lot-rate'] = 'Введите целое число больше нуля';
        }
    }
    if (null !== $lot_step) {
        if (!is_int($lot_step) || $lot_step <= 0) {
            $errors['lot-step'] = 'Введите целое число больше нуля';
        }
    }
    if (isset($_FILES['file']) && !($_FILES['file']['error'] === UPLOAD_ERR_OK)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_name = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_type = finfo_file($finfo, $file_name);
        if ($file_type !== 'image/png' || $file_type !== 'image/jpeg') {
            $errors['file'] = 'Загрузите картинку в нужном формате';
        } else {
            $file_path = 'uploads/' . $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
        }
    }

    if (count($errors) == 0) {
        $con = mysqli_connect("localhost", "root", "root", "yeticave");
        $file_path = 'uploads/' . $_FILES['file']['name'];
        $sql = "INSERT INTO lots (name, description, image_url, initial_price, completion_date, bet_step, categories_id) VALUES ((?), (?), (?), (?), (?), (?), (?))";
        $stmt = db_get_prepare_stmt(
            $con,
            $sql,
            $data = [$name, $message, $file_path, $lot_rate, $lot_date, $lot_step, $category]
        );
        mysqli_stmt_execute($stmt);
        //Редирект, если нет ошибок
        $last_id = mysqli_insert_id($con);
        header("Location: lot.php?id=" . $last_id);
        exit ();
    }
}


function readPOST($key)
{
    if (isset($_POST[$key]) && $_POST[$key]) {
        trim($_POST[$key]);
        if (empty($_POST[$key])) {
            return null;
        } else {
            return $_POST[$key];
        }
    } else {
        return null;
    };
}

$isAuth = rand(0, 1);



$categories = getCategories($con);


$pageContent = include_template('add-lot.php', compact('categories', 'errors'));
$page = include_template('layout.php', compact('categories','pageContent'));

print($page);

?>
