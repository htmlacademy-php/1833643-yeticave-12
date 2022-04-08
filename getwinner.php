<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'vendor/autoload.php';
require_once 'functions.php';
require_once 'db.php';

global $con;
$winners = calcWinners($con);
if (!empty($winners)) {
    foreach ($winners as $winner) {
        setWinners($con, $winner);
    }
}
/**
 * Checking the database for new winners.
 *
 * @param mysqli $con Connecting to the database.
 * @return array Returns array with data of each winner [lot id , winner id , lot nameа].
 */
function calcWinners(mysqli $con): array
{
    $winArr = [];
    $date = new DateTime();
    $current_date = $date->format('Y-m-d');
    $sql = "SELECT lots.id, bets.users_id as winner_id, lots.name as lot_name, users.email, users.name as winner_name
    FROM bets
    LEFT JOIN lots ON lots.id = bets.lots_id
    LEFT JOIN categories ON categories.id=lots.categories_id
    LEFT JOIN users ON users.id = lots.author_users_id
    WHERE amount IN (SELECT MAX(amount) amount FROM bets GROUP BY lots_id) AND lots.completion_date < ? AND lots.winner_users_id IS NULL";
    $stmt = db_get_prepare_stmt($con, $sql, [$current_date]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($result && $row = $result->fetch_assoc()) {
        $winArr[] = ['lot_id' => $row['id'],
            'winner_id' => $row['winner_id'],
            'lot_name' => $row['lot_name'],
            'winner_name' => $row['winner_name'],
            'winner_mail' => $row['email']
        ];
    }
    return $winArr;
}

/**
 * Marks the winner in the lot
 *
 * @param mysqli $con Connecting to the database.
 * @param array $winners Winners data array [lot id , winner id , lot nameа].
 */
function setWinners(mysqli $con, array $winners)
{
    $sql = "UPDATE lots SET winner_users_id=? WHERE id=?";
    $stmt = db_get_prepare_stmt($con, $sql, [(int)$winners['winner_id'], (int)$winners['lot_id']]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_errno($con) && $res) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        die();
    } else {
        sendCongratulations($winners);
    }
}

/**
 * Send victory message to the user's email
 *
 * @param array $winners Winner data array [lot id , winner id , lot name].
 */
function sendCongratulations(array $winners)
{
    // connect ini file
    $config = parse_ini_file('config.ini', true);
    $setFrom = $config['post']['setFrom'];
    $setPassword = $config['post']['setPassword'];
    $setHost = $config['post']['setHost'];
    $setPort = $config['post']['setPort'];
    $setEncryption = $config['post']['setEncryption'];
    $lotLink = $config['post']['lotLink'];
    $transport = (new Swift_SmtpTransport($setHost, $setPort, $setEncryption))
        ->setUsername($setFrom)
        ->setPassword($setPassword);
    $text = include_template('email.php', compact('winners', 'lotLink'));

    $message = (new Swift_Message())
        ->setSubject('Поздравления от Yeticave')
        ->setFrom([$setFrom])
        ->setTo($winners['winner_mail'])
        ->addPart($text, 'text/html');

    $mailer = new Swift_Mailer($transport);

}
