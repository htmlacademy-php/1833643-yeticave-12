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
 * Валидирует поле 'cost' в форме добавления ставки
 *
 * @param array $open_lot Массив с открытым лотом
 * @param int $current_price Текущая цена лота
 * @param array $session Данные из массива $_SESSION
 * @param array $post Данные из массива $_POST
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_field_cost(array $openLot, int $currentPrice, array $session, array $post): ?string
{
    $error = validateFilled('cost');
    if ($error) {
        return $error;
    }
    if (!isset($session['userId'])){
        return 'Необходимо зарегистрироваться';
    }
    if ($post['cost'] <= 0 || !is_numeric($post['cost'])) {
        return 'Начальная цена должна быть целым числом больше нуля';
    }

    $min_bet = $currentPrice + (int)$openLot['bet_step'];
    if ((int)$post['cost'] < $min_bet) {
        return 'Мин.ставка д.б. не менее ' . $min_bet .  ' ₽';
    }

    return NULL;
}

/**
 * Валидирует данные формы добавления ставки, получая данные из $POST
 *
 * @param array $openLot Ассоциативный массив с данными открытого лота
 * @param int $current_price Текущая цена лота
 * @param array $session Данные из массива $_SESSION
 * @param array $post Данные из массива $_POST
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validateBetsForm(array $openLot, int $currentPrice, array $session, array $post) : array
{
    $errors_validate['cost'] = validate_field_cost($openLot, $currentPrice, $session, $post);

    return array_filter($errors_validate);  //убираем пустые значения в массиве и возвращаем его
}
