<?php
require_once 'helpers.php';
require_once 'check_err.php';
require_once 'db.php';

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
        return $back_time[1] . get_noun_plural_form($back_time[1],' минута',' минуты',' минут') .' назад';
    }
    if ($diff < 86400) {
        return $back_time[0] . get_noun_plural_form($back_time[0],' час',' часа',' часов') .' назад';
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
function initOpenLot(array $get, array $session) : int
{
    if (isset($get['id'])) {
        $id = (int) $get['id'];

        if (isset($session['userId'])) {
            $_SESSION['lotId'] = $id;
        }
    }
    else {
        if (isset($session['lotId'])) {
            $id = (int)$session['lotId'];
        }
        else {
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
function checkPriceLot(array $bet_open_lot, int $start_price_lot) : int
{
    if (isset($bet_open_lot[0]['amount'])) {
        $current_price = (int)$bet_open_lot[0]['amount'];
    } else {
        $current_price = $start_price_lot;
    }
    return $current_price;
}

/**
 * Проверяет успешность выполнения записи в БД
 * Если нет, выдает ошибку
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param mysqli_stmt $stmt Подготовленное выражение
 *
 * @return void
 */
function check_success_insert_or_read_stmt_execute(mysqli $connection, mysqli_stmt $stmt) : void
{
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($connection);
        exit("Ошибка MySQL: " . $error);
    }
}

/**
 * Читает из БД все, согласно запросу, используя подготовленные выражения
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 * @param array $data Массив с данными для вставки на место плэйсхолдеров
 *
 * @return array Ассоциативный массив результата запроса
 */
function db_read_all_stmt(mysqli $connection, string $query, array $data) : ?array
{
    $stmt = db_get_prepare_stmt($connection, $query, $data);
    check_success_insert_or_read_stmt_execute($connection, $stmt);
    $result_query = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result_query, MYSQLI_ASSOC);
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
    $end_time = [floor($diff/3600), floor(($diff % 3600)/60), floor(($diff % 3600)%60)];

    if ($end_time[0] < 0 || $end_time[1] < 0 || $end_time[2] < 0) {
        $end_time[0] = '00';
        $end_time[1] = '00';
        $end_time[2] = '00';
        return $end_time;
    }

    if ($end_time[0] <10) {
        $end_time[0] = '0' . $end_time[0];
    }
    if ($end_time[1] <10) {
        $end_time[1] = '0' . $end_time[1];
    }
    if ($end_time[2] <10) {
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
 * @param string $date_create
 * @return string
 */
function get_dt_range_back(string $date_create): string
{
    $diff = strtotime("now") - strtotime($date_create);
    $back_time = [floor($diff/3600), floor(($diff % 3600)/60)];

    if ($diff < 3600) {
        return $back_time[1] . get_noun_plural_form($back_time[1], ' минута', ' минуты', ' минут') . ' назад';
    }
    if ($diff < 86400) {
        return $back_time[0] . get_noun_plural_form($back_time[0], ' час', ' часа', ' часов') . ' назад';
    }

    return date('d.m.y в H:i', strtotime($date_create));
}

/**
 * Return user name by id.
 * @param mysqli $con connect to BD.
 * @param int $id user id .
 * @return string|null user name.
 */
function getUserNameById(mysqli $con, int $id)
{
    if (is_null($id)){
        return null;
    }

    $sql = "SELECT name FROM users WHERE id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $name = null;

    if ($result && $row = $result->fetch_assoc()){
        $name = $row['name'];
    }
    return $name;
}

/**
 * Return user email by id.
 * @param mysqli $con connect to BD.
 * @param int $id user id .
 * @return string|null user mail.
 */
function getUserEmailById(mysqli $con,int $id)
{
    if (is_null($id)){
        return null;
    }

    $sql = "SELECT email FROM users WHERE id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $mail = null;

    if ($result && $row = $result->fetch_assoc()){
        $mail = $row['email'];
    }
    return $mail;
}
