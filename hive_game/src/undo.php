<?php

use Joyce0398\HiveGame\Utils;
use Joyce0398\HiveGame\GameLogic;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$lastMove = $_SESSION['last_move'];

[$board, $players] = Utils::createBoardAndPlayersFromSession($_SESSION);
try {
    $logic = new GameLogic($board);
    $moveIdBefore = $logic->undo($lastMove);
    $_SESSION['last_move'] = $moveIdBefore;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
