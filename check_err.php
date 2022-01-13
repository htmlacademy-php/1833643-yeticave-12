<?php
require_once 'helpers.php';

/**
 * @param $name
 * @param $min
 * @param $max
 * @return string|void
 */
function isCorrectLength($name, $min, $max)
{
    $len = mb_strlen($name);
    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }
}

/**
 * @param $name
 * @return string|void
 */
function validateFilled($name)
{
    if (empty($name)) {
        return "Это поле должно быть заполнено";
    }
}

/**
 * @param string $name
 * @return string|null
 */
function validateFilledGET(string $name): ?string
{
    if (empty($_GET[$name])) {
        return 'Поле не заполнено ';
    }

    return NULL;
}

/**
 * @param string $name
 * @return string
 */
function filteredGET(string $name): string
{
    return e($_GET[$name]) ?? "";
}

/**
 * @param $str
 * @return string|void
 */
function validateDate($date)
{
    if (null !== $date) {
        if (!is_date_valid($date)) {
            return "Неверный формат даты";
        } else {
            $time_now = strtotime("now");
            $experation_stamp = strtotime($date);
            $diff_time = $experation_stamp - $time_now;
            $day = 86400;
            if ($diff_time < $day) {
                return "Указанная дата должна быть больше текущей даты, хотя бы на один день";
            }
        }
    }
}

/**
 * @param $num
 * @return string|void
 */
function validateNaturalNum($num)
{
    if (null !== $num) {
        if (!is_int($num) || $num <= 0) {
            return "Введите целое число больше нуля";
        }
    }
}

/**
 * @param $key
 * @return mixed|null
 */
function readPOST($key)
{
    if (isset($_POST[$key]) && $_POST[$key]) {
        trim($_POST[$key]);
        if (empty($_POST[$key])) {
            return null;
        } else {
            return $_POST[$key];
        }
    } else {
        return null;
    }
}

/**
 * @param string $name
 * @return string
 */
function getPostVal(string $name): string
{
    return $_POST[$name] ?? "";
}

/**
 * @param string $name
 * @return string
 */
function getFilteredPostVal(string $name): string
{
    return e(getPostVal($name)) ?? "";
}

/**
 * @param array $hoursMinuts
 * @return string
 */
function getTimerValue(array $hoursMinuts): string
{
    return implode(':', $hoursMinuts) ?? "";
}

?>
