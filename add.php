<?php
session_start();
require_once 'helpers.php';
require_once 'check_err.php';
require_once 'db.php';

$required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["submit"])) {
    error_reporting(E_ALL);
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
    $lotRate = (int)readPOST('lot-rate');
    if (validateNaturalNum($lotRate)) {
        $errors['lot-rate'] = validateNaturalNum($lotRate);
    }

    $lotStep = (int)readPOST('lot-step');
    if (validateNaturalNum($lotStep)) {
        $errors['lot-step'] = validateNaturalNum($lotStep);
    }
    $date = date("Y-m-d H:i:s");
    $lotDate = readPOST('lot-date');
    if (validateDate($lotDate)) {
        $errors['lot-date'] = validateDate($date);
    }

    if (isset($_FILES['file']) && !($_FILES['file']['error'])) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileName = $_FILES['file']['tmp_name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = finfo_file($finfo, $fileName);
        if ($fileType == 'image/png' || $fileType == 'image/jpeg') {
            $filePath = 'uploads/' . $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $filePath);
        } else {
            $errors['file'] = 'Загрузите картинку в нужном формате';
        }
    }

    if (count($errors) == 0) {
        $con = mysqli_connect("localhost", "root", "root", "yeticave");
        $filePath = 'uploads/' . $_FILES['file']['name'];
        $sql = "INSERT INTO lots (name, description, image_url, initial_price, completion_date, bet_step, categories_id) VALUES ((?), (?), (?), (?), (?), (?), (?))";
        $stmt = db_get_prepare_stmt(
            $con,
            $sql,
            $data = [$name, $message, $filePath, $lotRate, $lotDate, $lotStep, $category]
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
$page = include_template('layout.php', compact('categories', 'pageContent'));

print($page);

?>
