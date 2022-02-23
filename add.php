<?php
session_start();
$userId = (int)$_SESSION['userId'];
global $con;
require_once 'helpers.php';
require_once 'check_err.php';
require_once 'db.php';
$title = 'Добавление лота';
$required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["submit"])) {
    error_reporting(E_ALL);

    ini_set('display_errors', 'on');
    foreach ($required_fields as $field) {
        if (readPOST($field) == null) {
            $errors[$field] = 'Поле не заполнено';
        }
    }
    $name = readPOST('lot-name');
    if (isCorrectLength($name, 1, 255)) {
        $errors['lot-name'] = isCorrectLength($name, 1, 255);
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

    if (validateDate()) {
        $errors['lot-date'] = validateDate();
    }

    try {

        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($_FILES['file']['error']) ||
            is_array($_FILES['file']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }

        // Check $_FILES['file']['error'] value.
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('Загрузите файл');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Неизвестные ошибки :-(');
        }

        // DO NOT TRUST $_FILES['file']['mime'] VALUE !!
        // Check MIME Type by yourself.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
                $finfo->file($_FILES['file']['tmp_name']),
                array(
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                ),
                true
            )) {
            $errors['file'] = 'Загрузите картинку в нужном формате';
        }

        // You should name it uniquely.
        // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
        if (!move_uploaded_file(
            $_FILES['file']['tmp_name'],
            sprintf('./uploads/%s.%s',
                sha1_file($_FILES['file']['tmp_name']),
                $ext
            )
        )) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        echo 'File is uploaded successfully.';

    } catch (RuntimeException $e) {

        $errors['file'] = $e->getMessage();

    }

    if (count($errors) === 0) {
        $filePath = 'uploads/' . $_FILES['file']['name'];
        $sql = "INSERT INTO lots (name, description, image_url, initial_price, completion_date, bet_step,author_users_id, categories_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = db_get_prepare_stmt($con, $sql,
            $data = [$name, $message, $filePath, $lotRate, $lotDate, $lotStep, $userId, $category]
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
$page = include_template('layout.php', compact('categories', 'pageContent','title'));

print($page);
