<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\BoardGame;
use Joyce0398\HiveGame\Database;

if (!isset($_SESSION['last_move'])) {
    $_SESSION['last_move'] = null;
}

$insertId = Database::pass($_SESSION['game_id'], $_SESSION['last_move'], BoardGame::getState());

$_SESSION['last_move'] = $insertId;
$_SESSION['player'] = 1 - $_SESSION['player'];

header('Location: index.php');
exit();
