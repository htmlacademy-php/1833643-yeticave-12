<?php
require_once 'helpers.php';
require_once 'check_err.php';
require_once 'db.php';
global $con;
$categories = getCategories($con);
$title = 'Вход';


$rules = [
    'email' => function () use ($con): ?string {
        $error = validateFilled('email');
        if ($error) {
            return $error;
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Неверный формат адреса электронной почты. Проверьте введенный email';
        }
        $mail = getPostVal('email');
        $sql = "SELECT email FROM users WHERE email = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 's', $mail);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existEmail = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (!$existEmail) {
            return 'Пользователь с таким email еще не зарегистрирован';
        }

        return NULL;
    },
    'password' => function () use ($con): ?string {
        $error = validateFilled('password');
        if ($error) {
            return $error;
        }

        $sql = "SELECT password FROM users WHERE email = ?";
        $stmt = mysqli_prepare($con, $sql);
        $email = getPostVal('email');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existEmail = mysqli_fetch_assoc($result);

        if (!password_verify(getPostVal('password'), $existEmail['password'])) {
            return 'Вы ввели неверный пароль';
        }

        return NULL;
    }
];

$errors = [];

if (isset($_POST['submit'])) {  //If there is such a field in the POST, then the form has been sent

    //Validation of relevant fields and saving errors (if any)
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    $errors = array_filter($errors);  //removing empty values in the array
    //If there were validation errors, we return to the page for adding a new account with errors displayed.
    if ($errors) {
        $pageContent = include_template('login.php', compact('categories', 'errors'));
        $page = include_template('layout.php', compact('categories', 'pageContent', 'title'));
        print($page);
    } else {
        $sql = "SELECT id,name FROM users WHERE email = ?";
        $stmt = mysqli_prepare($con, $sql);
        $email = getPostVal('email');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $resultUser = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($resultUser);
        if (!mysqli_stmt_execute($stmt)) {
            $error = mysqli_error($con);
            exit("Ошибка MySQL: " . $error);
        }
        session_start();
        $_SESSION['userId'] = $user['id'];
        $_SESSION['userName'] = $user['name'];
        if (isset($_SESSION['userName'])) {
            print($_SESSION['userName']);
        }

        // redirect to the login page of personal account
        header('Location: index.php');
    }
} else {  //If the form has not submitted,  show the account creation page
    $pageContent = include_template('login.php', compact('categories', 'errors'));
    $page = include_template('layout.php', compact('categories', 'pageContent', 'title'));
    print($page);
}
