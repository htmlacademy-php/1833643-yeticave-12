<?php
require_once 'helpers.php';
require_once 'check_err.php';
require_once 'db.php';

$categories = getCategories($con);
$title = 'Регистрация нового аккаунта';

$rules = [
    'email' => function () use ($con): ?string {
        $error = validateFilled('email');
        if ($error) {
            return $error;
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Неверный формат адреса электронной почты. Проверьте введенный email';
        }

        $sql_read_email_users = "SELECT users.email FROM users WHERE users.email = ?";
        $stmt = mysqli_prepare($con, $sql_read_email_users);
        mysqli_stmt_bind_param($stmt, 's', getPostVal('email'));
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existEmail = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if ($existEmail) {
            return 'Пользователь с таким email уже зарегистрирован';
        }

        return NULL;
    },
    'password' => function (): ?string {
        return validateFilled('password');
    },
    'name' => function (): ?string {
        return validateFilled('name');
    },
    'message' => function (): ?string {
        return validateFilled('message');
    }
];

$errors = [];

if (isset($_POST['submit'])) {  //If there is such a field in the POST, then the form has been sent
    $hash_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    //Validation of relevant fields and saving errors (if any)
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    $errors = array_filter($errors);  //removing empty values in the array

    //if errors - show add account page
    if ($errors) {
        $pageContent = include_template('sign-up.php', compact('categories', 'errors'));
        $page = include_template('layout.php', compact('categories', 'pageContent', 'title'));
        print($page);
    } else {
        //if not errors - add new user to database
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $sql = "INSERT INTO users (email, name, password, contacts) VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', getPostVal('email'), getPostVal('name'), $hash_password, getPostVal('message'));
        if (!mysqli_stmt_execute($stmt)) {
            $error = mysqli_error($con);
            exit("Ошибка MySQL: " . $error);
        }

        //redirect to user personal page
        header('Location: sign-in.php');
    }
} else {  //if form not sent, show add account page
    $pageContent = include_template('sign-up.php', compact('categories', 'errors'));
    $page = include_template('layout.php', compact('categories', 'pageContent', 'title'));
    print($page);
}
