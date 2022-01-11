<?php
$con = mysqli_connect("localhost", "root", "root", "yeticave");
session_start();
require_once 'helpers.php';
require_once 'check_err.php';
$title = 'Вход';
$numberLotsOnPage = 9;

$categories = getCategories($con);

$rules = [
    'search' => function() : ?string {
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
        $pageContent = include_template('temp-search.php', compact('categories' , 'errors' ));
        $page = include_template('layout.php', compact('categories', 'pageContent', 'title' ));
        print($page);
    }
    else {
        $searchQuery = trim($_GET['search']);

        //get the number of lots found by the search
        $sql = "SELECT lots.id, lots.created_at, lots.name, lots.description FROM lots WHERE (MATCH(lots.name, lots.description) AGAINST(?)) AND (lots.completion_date > NOW()) ORDER BY lots.created_at DESC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 's', $searchQuery);
        mysqli_stmt_execute($stmt);
        $searchedLots = mysqli_stmt_get_result($stmt);
        $numberOfSearchedLots = mysqli_num_rows($searchedLots);

        if (isset($_GET['page'])) {
            $activePage = $_GET['page'];
        }
        else {
            $activePage = 1;
        }

        //Calculation of parameters for find lots
        $offset = ((int)$activePagee - 1) * DEFAULT_LOTS_ON_PAGE;
        $numberOfPage = (int)ceil($numberOfSearchedLots / DEFAULT_LOTS_ON_PAGE);

        //Getting limited search list of lots
        $sql = "SELECT lots.id, lots.created_at, lots.name, lots.description, lots.image_url, lots.initial_price, lots.completion_date, lots.bet_step, categories.name AS name_category FROM lots JOIN categories ON lots.categories_id = categories.id WHERE (MATCH(lots.name, lots.description) AGAINST(?)) AND (lots.completion_date > NOW()) ORDER BY lots.completion_date DESC LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'sii', $searchQuery, $numberLotsOnPage, $offset);
        mysqli_stmt_execute($stmt);
        $result_query_search = mysqli_stmt_get_result($stmt);
        $searchResults = mysqli_fetch_all($result_query_search, MYSQLI_ASSOC);

        if (!mysqli_stmt_execute($stmt)) {
            $error = mysqli_error($con);
            exit("Ошибка MySQL:" . $error);
        }

        $pageContent = include_template('temp-search.php', compact('categories' , 'errors' , 'searchResults', 'numberLotsOnPage' , 'numberOfSearchedLots' , 'numberOfPage' , 'activePage' ));
        $page = include_template('layout.php', compact('categories', 'pageContent' , 'title'));
        print($page);

    }
}
else {  //If the form is not submitted, show the results page without results
    $pageContent = include_template('temp-search.php', compact('categories', 'errors' , 'searchResults' , 'numberLotsOnPage', 'numberOfSearchedLots' , 'numberOfPage' , 'activePage' ));
    $page = include_template('layout.php', compact('categories', 'pageContent' , 'title' ));
    print($page);
}

?>
