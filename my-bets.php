<?php
session_start();
require_once 'helpers.php';
require_once('functions.php');
require_once('db.php');
global $con;

$userId = (int)$_SESSION['userId'];
$categories = getCategories($con);
$lotsWithMyBets = getAllLotsWithMyBets($con, $userId);
$title = 'Мои ставки';

$pageContent = include_template('t-my-bets.php', compact('categories', 'lotsWithMyBets'));
$page = include_template('layout.php', compact('categories', 'pageContent', 'title'));
print($page);

