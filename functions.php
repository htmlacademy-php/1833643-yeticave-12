<?php

/**
 * @param string $create_at
 * @return string
 * how long has the bet been made
 */
function timeAgo(string $create_at): string
{
    $diff = strtotime("now") - strtotime($create_at);
    $back_time = [floor($diff / 3600), floor(($diff % 3600) / 60)];
    if ($diff < 3600) {
        return $back_time[1] . get_noun_plural_form($back_time[1], ' минута', ' минуты', ' минут') . ' назад';
    }
    if ($diff < 86400) {
        return $back_time[0] . get_noun_plural_form($back_time[0], ' час', ' часа', ' часов') . ' назад';
    }
    return date('d.m.Y в H:i', strtotime($create_at));
}

/**
 * Проверяет указан ли id лота в $GET или в $SESSION и возвращает id лота
 * Если открыта сессия - прописывает в нее id лота *
 *
 * @param array $get Данные из массива $_GET
 * @param array $session Данные из массива $_SESSION
 *
 * @return int Возвращает id лота
 */
function initOpenLot(array $get, array $session): int
{
    if (isset($get['id'])) {
        $id = (int)$get['id'];

        if (isset($session['userId'])) {
            $_SESSION['lotId'] = $id;
        }
    } else {
        if (isset($session['lotId'])) {
            $id = (int)$session['lotId'];
        } else {
            http_response_code(404);
            exit("Ошибка подключения: не указан id");
        }
    }
    return $id;
}

/**
 * Checks whether bets have already been placed on this lot
 * If done, indicates it as the lot price
 *
 * @param array $bet_open_lot Array with open lot bets
 * @param int $start_price_lot Starting price of the lot
 *
 * @return int Returns the current lot price
 */
function checkPriceLot(array $bet_open_lot, int $start_price_lot): int
{
    if (isset($bet_open_lot[0]['amount'])) {
        $current_price = (int)$bet_open_lot[0]['amount'];
    } else {
        $current_price = $start_price_lot;
    }
    return $current_price;
}

/**
 * Получает разницу между текущим временем и поданным на вход.
 * Данная функция возвращает разницу включая секунды
 *
 * @param string $date_end Время в виде строки
 *
 * @return array Возвращает часы, минуты и секунды в массиве
 */
function get_dt_range_with_seconds(string $date_end): array
{
    $diff = strtotime($date_end) - strtotime("now");
    $end_time = [floor($diff / 3600), floor(($diff % 3600) / 60), floor(($diff % 3600) % 60)];

    if ($end_time[0] < 0 || $end_time[1] < 0 || $end_time[2] < 0) {
        $end_time[0] = '00';
        $end_time[1] = '00';
        $end_time[2] = '00';
        return $end_time;
    }

    if ($end_time[0] < 10) {
        $end_time[0] = '0' . $end_time[0];
    }
    if ($end_time[1] < 10) {
        $end_time[1] = '0' . $end_time[1];
    }
    if ($end_time[2] < 10) {
        $end_time[2] = '0' . $end_time[2];
    }

    return $end_time;
}

/**
 * Форматирует время поданное на вход
 * Вставляет двоеточие между часами и минутами
 *
 * @param array $hours_and_minuts Массив с часами и минутами
 *
 * @return string
 */
function getTimerValue(array $hours_and_minuts): string
{
    return implode(':', $hours_and_minuts) ?? "";
}


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

/**
 * format sum view for example 1 000
 *
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

/**
 * защита от XSS
 *
 * @param $string
 * @return string
 */
function e($string):string
{
    if (isset($string)){
        return htmlspecialchars($string);
    }
    return null;
}
