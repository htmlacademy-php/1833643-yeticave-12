<?php

require_once('helpers.php');
$is_auth = rand(0, 1);
$user_name = 'IgorGrinev'; // укажите здесь ваше имя

function format_amount($amount)
{
    $amount = ceil($amount);
    if ($amount >= 1000) {
        $amount = number_format($amount, 0, ".", " ");
    }
    $result = "{$amount} ₽";
    return $result;
}

$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];
$units = [
    [
        'name' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 10999,
        'image' => 'img/lot-1.jpg'
    ],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 159999,
        'image' => 'img/lot-2.jpg'
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => 8000,
        'image' => 'img/lot-3.jpg'
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => 10999,
        'image' => 'img/lot-4.jpg'
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => 7500,
        'image' => 'img/lot-5.jpg'
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => 5400,
        'image' => 'img/lot-6.jpg'
    ]
];
$page_content = include_template('main.php', $data = ['categories' => $categories, 'units' => $units]);
$page = include_template('layout.php',
                         $data = [
                             'categories' => $categories,
                             'units' => $units,
                             'page_content' => $page_content,
                             'page_name' => 'Главная',
                             'user_name' => $user_name,
                             'is_auth' => $is_auth
                         ]
);
print($page);
?>
