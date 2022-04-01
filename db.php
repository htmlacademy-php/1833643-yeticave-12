<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$config = parse_ini_file('config.ini', true);
$hostname = $config['db']['hostname'];
$username = $config['db']['username'];
$password = $config['db']['password'];
$database = $config['db']['database'];
$con = mysqli_connect($hostname, $username, $password, $database);

/**
 * @param $searchQuery
 * @param $con
 * @return int
 */
function numberOfSearchedLots_($searchQuery, $con): int
{
    $sql = "SELECT lots.id, lots.created_at, lots.name, lots.description FROM lots WHERE (MATCH(lots.name, lots.description) AGAINST(?)) AND (lots.completion_date > NOW()) ORDER BY lots.created_at DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $searchQuery);
    mysqli_stmt_execute($stmt);
    $searchedLots = mysqli_stmt_get_result($stmt);

    if (mysqli_errno($con) && $searchedLots) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        die();
    } else {
        return mysqli_num_rows($searchedLots);
    }
}

/**
 * @param $con
 * @param $searchQuery
 * @param $numberLotsOnPage
 * @param $offset
 * @return ?array
 */
function searchResults($con, $searchQuery, $numberLotsOnPage, $offset): ?array
{
    if (isset($_GET['find'])) {
        $sql = "SELECT lots.id, lots.created_at, lots.name, lots.description, lots.image_url, lots.initial_price, lots.completion_date, lots.bet_step, categories.name AS name_category FROM lots JOIN categories ON lots.categories_id = categories.id WHERE (MATCH(lots.name, lots.description) AGAINST(?)) AND (lots.completion_date > NOW()) ORDER BY lots.completion_date DESC LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'sii', $searchQuery, $numberLotsOnPage, $offset);
        mysqli_stmt_execute($stmt);
        $result_query_search = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result_query_search, MYSQLI_ASSOC);
    }
    return null;
}

/**
 * get array of categories
 *
 * @param mysqli $con
 * @return array
 */
function getCategories(mysqli $con): array
{
    $sql = "SELECT
       id,
       name,
       symbol_code
FROM categories";
    $result = mysqli_query($con, $sql);
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if (mysqli_errno($con) && $result) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        die();
    } else {
        return $categories;
    }
}

/**
 * @param mysqli $con
 * @param $id
 * @return void
 */
function checkId(mysqli $con, $id)
{
    $sql = "SELECT id FROM lots WHERE id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($res) === 0) {
        header('Location: pages/404.html');
    }
}

/**
 * @param $con
 * @param $lotId
 * @param $userId
 * @return true/false
 */

function checkMyLot($con, $lotId,$userId) :bool
{
    $sql = "SELECT id FROM lots WHERE id = ? and author_users_id = ? ";
    $stmt = db_get_prepare_stmt($con, $sql, [$lotId, $userId]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($res) === 0) {
        return false;
    }else{
        return true;
    }
}


/**
 * @param mysqli $con
 * @param $id
 * @return array
 */
function getUnit(mysqli $con, $id): array
{
    $sql = "SELECT lots.id,
       lots.name as name,
       lots.image_url,
       lots.completion_date,
       lots.initial_price,
       lots.description,
       categories.name as category
        FROM lots
        INNER JOIN categories ON lots.categories_id = categories.id
        WHERE lots.id = ?";
    $unit = [];
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && $row = $res->fetch_assoc()) {
        $unit = $row;
    }

    return $unit;
}

/**
 * Записывает в БД указанному лоту победителя по нему
 *
 * @param mysqli $connection Connect BD
 * @param int $userId id of current user
 *
 * @return array Вышеозначенные лоты
 */
function getAllLotsWithMyBets(mysqli $connection, int $userId): array
{
    $sql_lots_with_my_bets = "SELECT lots.id AS lot_id,
       lots.created_at AS date_create_lot,
       lots.name,
       lots.image_url,
       lots.initial_price,
       lots.completion_date,
       lots.bet_step,
       lots.winner_users_id,
       categories.name AS category_name,
       bets.users_id,
       bets.created_at AS date_create_bet,
       bets.amount AS price_my_bet,
       users.contacts
FROM lots JOIN categories ON lots.categories_id = categories.id
    JOIN bets ON lots.id = bets.lots_id LEFT JOIN users ON lots.winner_users_id = users.id
WHERE (bets.users_id = ?) ORDER BY bets.created_at DESC";

    if (mysqli_errno($connection) && db_read_all_stmt($connection, $sql_lots_with_my_bets, [$userId])) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        die();
    }
    return db_read_all_stmt($connection, $sql_lots_with_my_bets, [$userId]);
}

/**
 * Get current lot data
 * @param $connection
 * @param $lotId
 * @return array|false|string[]|void|null
 */

function openLot($connection, $lotId)
{
    $sql = "SELECT
       lots.id,
       lots.created_at,
       lots.name,
       lots.description,
       lots.image_url,
       lots.initial_price,
       lots.completion_date,
       lots.bet_step,
       categories.name AS name_category
FROM lots JOIN categories ON lots.categories_id = categories.id
WHERE lots.id =?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $lotId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_errno($connection) && $result) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        die();
    } else {
        return mysqli_fetch_assoc($result);
    }
}

/**
 * get all bets of current lot
 * @param $connection
 * @param $lotId
 * @return array
 */
function openBets($connection, $lotId): array
{
    $sql = "SELECT
       bets.created_at,
       bets.amount,
       users.name
FROM bets JOIN users ON bets.users_id = users.id
WHERE bets.lots_id = ?
ORDER BY bets.created_at DESC ";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $lotId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_errno($connection) && $result) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        die();
    } else {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }


}

/**
 * Return category name by id.
 * @param mysqli $con connect to BD.
 * @param string $code category code .
 * @return string category name.
 */
function getCategoryNameByCode(mysqli $con, string $code): string
{
    $sql = "SELECT name FROM categories WHERE symbol_code = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$code]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $name = null;

    if ($result && $row = $result->fetch_assoc()) {
        $name = $row['name'];
    }
    return $name;
}

/**
 *
 *
 *
 * @return array/null Ассоциативный массив результата запроса
 */

/**
 * Читает из БД все, согласно запросу, используя подготовленные выражения
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 * @param array $data Массив с данными для вставки на место плэйсхолдеров
 * @return array|null
 */
function db_read_all_stmt(mysqli $connection, string $query, array $data): ?array
{
    $stmt = db_get_prepare_stmt($connection, $query, $data);
    check_success_insert_or_read_stmt_execute($connection, $stmt);
    $result_query = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result_query, MYSQLI_ASSOC);
}

/**
 * Проверяет успешность выполнения записи в БД
 * Если нет, выдает ошибку
 *
 * @param mysqli $connection Ресурс соединения
 * @param mysqli_stmt $stmt Подготовленное выражение
 *
 * @return void
 */
function check_success_insert_or_read_stmt_execute(mysqli $connection, mysqli_stmt $stmt): void
{
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($connection);
        exit("Ошибка MySQL: " . $error);
    }
}
