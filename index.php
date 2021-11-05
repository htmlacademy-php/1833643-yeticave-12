<?php

$con = mysqli_connect("localhost", "root", "", "yeticave");
//$sql = "SELECT id, name FROM lots";
//$result = mysqli_query($con, $sql);
//$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once('helpers.php');
define("TIMEZONE", "Europe/Kaliningrad");
$isAuth = rand(0, 1);
$userName = 'IgorGrinev'; // укажите здесь ваше имя

//защита от XSS
function e($string)
{
    return htmlspecialchars($string);
}

/**
 * @param int $amount
 * @return string
 */
function formatAmount(int $amount): string
{
    $amount = ceil($amount);
    if ($amount >= 1000) {
        $amount = number_format($amount, 0, ".", " ");
    }
    return "{$amount} ₽";
}

//testing of function. I forgot to remove the test result.


/**
 *
 * @param string $finTime => 'ГГГГ-ММ-ДД'
 * @return array['$h' => 'string','$m' => 'string'])]
 *
 */
#[ArrayShape (['$h' => 'string', '$m' => 'string'])]
function countdown(string $finTime): array //однообразил с ключом массива Units
{
    date_default_timezone_set(TIMEZONE);
    $deadline = strtotime($finTime);//дата истечения срока
    $currentTime = time();//текущее время
    if ($deadline > $currentTime) {
        $s = $deadline - $currentTime;//осталось секунд до дедлайна
        $h = floor($s / 60 / 60);//осталось часов
        $m = floor($s / 60) - ($h * 60);//осталось минут
        if ($h < 10) {
            $h = str_pad($h, 2, "0", STR_PAD_LEFT); // Добавляем лидирующий ноль
        }
        if ($m < 10) {
            $m = str_pad($m, 2, "0", STR_PAD_LEFT); // Добавляем лидирующий ноль
        }
    } else {
        $h = $m = '00';//время вышло
    }

    return [(string)$h, (string)$m];
}

$sql = "SELECT name, symbol_code FROM categories";
$result = mysqli_query($con, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql = "SELECT lots.id,
            lots.name            as name,
            lots.image_url            as image,
            lots.initial_price        as price,
            categories.name      as category,
            lots.completion_date      as finTime
        FROM lots
        INNER JOIN categories ON lots.categories_id = categories.id
    WHERE completion_date > now() ";
$result = mysqli_query($con, $sql);
$units = mysqli_fetch_all($result, MYSQLI_ASSOC);


$title = 'Главная';
$pageContent = include_template('main.php', compact('categories', 'units'));
$page = include_template(
    'layout.php',
    compact('categories', 'categories', 'units', 'pageContent', 'title', 'userName', 'isAuth')
);
print($page);
?>
