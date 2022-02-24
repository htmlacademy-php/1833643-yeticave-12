<?php
$config = parse_ini_file('config.ini', true);
$timezone = $config['location']['timezone'];

define("TIMEZONE", $timezone);
require_once 'functions.php';
require_once 'helpers.php';
require_once 'db.php';
require_once 'check_err.php';
