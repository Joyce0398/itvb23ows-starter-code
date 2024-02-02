<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\Database;
use Joyce0398\HiveGame\Hand;

$_SESSION['board'] = [];
$_SESSION['hand'] = [Hand::$DEFAULT_HAND, Hand::$DEFAULT_HAND];
$_SESSION['player'] = 0;

$insertId = Database::restart();
$_SESSION['game_id'] = $insertId;

header('Location: index.php');
