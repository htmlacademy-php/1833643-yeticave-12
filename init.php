<?php
$config = parse_ini_file('config.ini', true);
$timezone = $config['location']['timezone'];

define("TIMEZONE", $timezone);
