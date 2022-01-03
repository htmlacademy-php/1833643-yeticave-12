<?php

$con = mysqli_connect("localhost", "root", "root", "yeticave");
require_once('helpers.php');
require_once 'check_err.php';
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
    if (isCorrectLength($name, 1, 10)) {
        $errors['lot-name'] = isCorrectLength($name, 1, 10);
    }
    $category = (int)readPOST('category');
    $message = readPOST('message');
    $lot_rate = (int)readPOST('lot-rate');
    if (validateNaturalNum($lot_rate)) {
        $errors['lot-rate'] = validateNaturalNum($lot_rate);
    }

    $lot_step = (int)readPOST('lot-step');
    if (validateNaturalNum($lot_step)) {
        $errors['lot-step'] = validateNaturalNum($lot_step);
    }
    $lot_date = readPOST('lot-date');
    if (validateDate($lot_date)) {
        $errors['lot-date'] = validateDate($date);
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
        //Redirect if not errors
        $last_id = mysqli_insert_id($con);
        header("Location: lot.php?id=" . $last_id);
        exit ();
    }
}

$categories = getCategories($con);

$pageContent = include_template('add-lot.php', compact('categories', 'errors'));
$page = include_template('layout.php', compact('categories', 'pageContent','isAuth','userName'));

print($page);

?>
