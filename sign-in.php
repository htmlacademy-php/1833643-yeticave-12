<?php
$con = mysqli_connect("localhost", "root", "root", "yeticave");
require_once 'helpers.php';
require_once 'check_err.php';

$categories = getCategories($con);
//$isAuth = 0;
//$userName = 'IgorGrinev'; // enter your name here
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

        $sql = "SELECT email FROM users WHERE email = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 's', getPostVal('email'));
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        print_r($result);
        $exist_email = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (!$exist_email) {
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
        mysqli_stmt_bind_param($stmt, 's', getPostVal('email'));
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exist_email = mysqli_fetch_assoc($result);

        if (!password_verify(getPostVal('password'), $exist_email['password'])) {
            return 'Вы ввели неверный пароль';
        }

        return NULL;
    }
];

$errors = [];

if (isset($_POST['submit'])) {  //Если есть такое поле в POST, значит форма отправлена

    //Валидация соответствующих полей и сохранение ошибок (при наличии)
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    $errors = array_filter($errors);  //убираем пустые значения в массиве
    //Если были ошибки валидации - возвращаем на страницу добавления нового аккаунта с показом ошибок
    if ($errors) {
        $pageContent = include_template('login.php', compact('categories', 'errors'));
        $page = include_template('layout.php', compact('categories', 'pageContent', 'title'
        //, 'userName' , 'isAuth'
        ));
        print($page);
    } else {
        $sql = "SELECT id,name FROM users WHERE email = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 's', getPostVal('email'));
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

        //Перенаправляем на страницу входа в личный кабинет
        header('Location: index.php');
    }
} else {  //Если форма не отправлена, показываем страницу создания аккаунта
    $pageContent = include_template('login.php', compact('categories', 'errors'));
    $page = include_template('layout.php', compact('categories', 'pageContent', 'title'));
    print($page);
}
