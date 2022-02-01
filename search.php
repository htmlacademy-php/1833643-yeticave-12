<?php
session_start();
require_once 'helpers.php';
require_once 'functions.php';
require_once 'check_err.php';
require_once 'db.php';
$title = 'Вход';
$numberLotsOnPage = 9;

$categories = getCategories($con);

$rules = [
    'search' => function (): ?string {
        return validateFilledGET('search');
    }
];

$errors = [];

if (isset($_GET['find'])) {  //If there is such a field in GET, then the form has been sent

    //Validation of relevant fields and saving errors (if any)
    foreach ($_GET as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    $errors = array_filter($errors);  //removing empty values in the array

    //If validation errors, show the search results page
    if ($errors) {
        $pageContent = include_template('t-search.php', compact('categories', 'errors'));
        $page = include_template('layout.php', compact('categories', 'pageContent', 'title'));
        print($page);
    } else {
        $searchQuery = trim($_GET['search']);

        //get the number of lots found by the search
        $numberOfSearchedLots = numberOfSearchedLots_($searchQuery, $con);
        $activePage = $_GET['page'] ?? $activePage ?? 1;

        //Calculation of parameters for find lots
        $offset = ((int)$activePage - 1) * $numberLotsOnPage;
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

?>
