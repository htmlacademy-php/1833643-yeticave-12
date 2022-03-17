<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
session_start();
require_once 'init.php';
global $con;
$title = 'Поиск';
$numberLotsOnPage = 9;
$searchResults = array();
$numberOfSearchedLots = 0;
$numberOfPage = 0;
$activePage = 0;
$offset = 0;
$categories = getCategories($con);
$searchResults = [];

$errors = [];
$rules = [
    'search' => function () {
        return validateFilledGet('find');
    }
];

if (isset($_GET['search'])) {  //If there is such a field in GET, then the form has been sent

    //Validation of relevant fields and saving errors (if any)
    foreach ($_GET as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    $errors = array_filter($errors);  //removing empty values in the array

    //If validation errors, show the search results page
    {
        $searchQuery = trim($_GET['search']);

        //get the number of lots found by the search
        $numberOfSearchedLots = numberOfSearchedLots_($searchQuery, $con);
        $activePage = $_GET['page'] ?? $activePage ?? 1;

        //Calculation of parameters for find lots

        $numberOfPage = (int)ceil($numberOfSearchedLots / $numberLotsOnPage);

        //Getting limited search list of lots
        $searchResults = searchResults($con, $searchQuery, $numberLotsOnPage, $offset);

        $pageContent = include_template('t-search.php', compact('categories', 'errors', 'searchResults', 'numberLotsOnPage', 'numberOfSearchedLots', 'numberOfPage', 'activePage'));
        $page = include_template('layout.php', compact('categories', 'pageContent', 'title'));
        print($page);

    }
} else {  //If the form is not submitted, show the results page without results

    $pageContent = include_template('t-search.php', compact('categories', 'errors', 'searchResults', 'numberLotsOnPage', 'numberOfSearchedLots', 'numberOfPage', 'activePage'));
    $page = include_template('layout.php', compact('categories', 'pageContent', 'title'));
    print($page);
}
