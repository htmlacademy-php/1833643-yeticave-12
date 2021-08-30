<?php

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

$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];
$units = [
    [
        'name' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 10999,
        'finTime' => '2021-08-23',
        'image' => 'img/lot-1.jpg'
    ],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 159999,
        'finTime' => '2021-08-18',
        'image' => 'img/lot-2.jpg'
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => 8000,
        'finTime' => '2021-08-19',
        'image' => 'img/lot-3.jpg'
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => 10999,
        'finTime' => '2021-08-20',
        'image' => 'img/lot-4.jpg'
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => 7500,
        'finTime' => '2021-08-21',
        'image' => 'img/lot-5.jpg'
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => 5400,
        'finTime' => '2021-08-22',
        'image' => 'img/lot-6.jpg'
    ]
];
$title = 'Главная';
$pageContent = include_template('main.php', compact('categories', 'units'));
$page = include_template(
    'layout.php',
    compact('categories', 'categories', 'units', 'pageContent', 'title', 'userName', 'isAuth')
);
print($page);
?>
