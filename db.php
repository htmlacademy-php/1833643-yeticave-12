<?php

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
    $sql = "SELECT name, symbol_code as id FROM categories";
    $categories = [];
    $res = mysqli_query($con, $sql);
    while ($res && $row = $res->fetch_assoc()) {
        $categories[] = $row;
    }
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

?>
