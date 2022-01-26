<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$con = mysqli_connect("localhost", "root", "root", "yeticave");

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
    return mysqli_num_rows($searchedLots);
}

/**
 * @param $con
 * @param $searchQuery
 * @param $numberLotsOnPage
 * @param $offset
 * @return array
 */
function searchResults($con, $searchQuery, $numberLotsOnPage, $offset)
{
    $sql = "SELECT lots.id, lots.created_at, lots.name, lots.description, lots.image_url, lots.initial_price, lots.completion_date, lots.bet_step, categories.name AS name_category FROM lots JOIN categories ON lots.categories_id = categories.id WHERE (MATCH(lots.name, lots.description) AGAINST(?)) AND (lots.completion_date > NOW()) ORDER BY lots.completion_date DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sii', $searchQuery, $numberLotsOnPage, $offset);
    mysqli_stmt_execute($stmt);
    $result_query_search = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result_query_search, MYSQLI_ASSOC);
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($con);
        return ("Ошибка MySQL:" . $error);
    }
}

/**
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
    return $categories;
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
    if (mysqli_num_rows($res) == 0) {
        header('Location: pages/404.html');
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
 * @param mysqli $connection
 * @param string $query
 * @return array|null
 */
function dbReadAll(mysqli $connection, string $query): ?array
{
    $result_query = mysqli_query($connection, $query);
    return mysqli_fetch_all($result_query, MYSQLI_ASSOC);
}

/**
 * @param mysqli $connection
 * @param string $query
 * @return array|null
 */
function dbReadOneLine(mysqli $connection, string $query): ?array
{
    $result_query = mysqli_query($connection, $query);
    return mysqli_fetch_array($result_query, MYSQLI_ASSOC);
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
    return db_read_all_stmt($connection, $sql_lots_with_my_bets, [$userId]);
}

/**
 * Get current lot data
 * @param $connnection
 * @param $lotId
 * @return array|false|string[]|null
 */
function openLot($connnection, $lotId)
{
    $sql = "SELECT
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
    $stmt = mysqli_prepare($connnection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $lotId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

/**
 * get all bets of current lot
 * @param $connnection
 * @param $lotId
 * @return array
 */
function openBets($connection, $lotId)
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
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
