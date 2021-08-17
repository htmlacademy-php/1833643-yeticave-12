<?php

require_once('helpers.php');
$is_auth = rand(0, 1);
$user_name = 'IgorGrinev'; // укажите здесь ваше имя

//защита от XSS
function e($string)
{
    return htmlspecialchars($string);
}

function format_amount($amount)
{
    $amount = ceil($amount);
    if ($amount >= 1000) {
        $amount = number_format($amount, 0, ".", " ");
    }
    /** А что не так здесь?
     * посмотрел мануал PSR12. Там if так форматируется, отступ блока четыре пробела.
     */
    return "{$amount} ₽";
}

function countdown($date)
{
    //date_default_timezone_set('Europe/Moscow');
    date_default_timezone_set('Europe/Kaliningrad');
    $fin_date = strtotime($date);//дата истечения срока
    $today = time();//текущее время
    $h = '00';//по умолчанию часы вышли
    $m = '00';//по умолчанию минуты вышли
    if ($fin_date > $today) {
        $s = $fin_date - $today;//осталось секунд
        $h = floor($s / 60 / 60);//осталось часов
        $m = floor($s / 60) - ($h * 60);//осталось минут
        if ($h < 10) {
            $h = str_pad($h, 2, "0", STR_PAD_LEFT); // Добавляем лидирующий ноль
        }
        if ($m < 10) {
            $m = str_pad($m, 2, "0", STR_PAD_LEFT); // Добавляем лидирующий ноль
        }
    }

    //return [$hours, $minutes];;
    return [$h, $m];
}

$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];
$units = [
    [
        'name' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 10999,
        'fin_time' => '2021-08-23',
        'image' => 'img/lot-1.jpg'
    ],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 159999,
        'fin_time' => '2021-08-18',
        'image' => 'img/lot-2.jpg'
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => 8000,
        'fin_time' => '2021-08-19',
        'image' => 'img/lot-3.jpg'
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => 10999,
        'fin_time' => '2021-08-20',
        'image' => 'img/lot-4.jpg'
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => 7500,
        'fin_time' => '2021-08-21',
        'image' => 'img/lot-5.jpg'
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => 5400,
        'fin_time' => '2021-08-22',
        'image' => 'img/lot-6.jpg'
    ]
];
$title = 'Главная';
$page_content = include_template('main.php', compact('categories', 'units'));
$page = include_template(
    'layout.php',
    compact('categories', 'categories', 'units', 'page_content', 'title', 'user_name', 'is_auth')
);
print($page);
?>
