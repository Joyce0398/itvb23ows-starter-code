<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\GameLogic;
use Joyce0398\HiveGame\Utils;
use Joyce0398\HiveGame\Database;

$gameId = $_SESSION['game_id'];
$lastMove = $_SESSION['last_move'] ?? null;

[$board, $players] = Utils::createBoardAndPlayersFromSession($_SESSION);
$currentPlayer = $players[$_SESSION['player']];

$gameLogic = new GameLogic($board);

if (!isset($_SESSION['last_move'])) {
    $_SESSION['last_move'] = null;
}
try {
    $gameLogic->checkSkip($currentPlayer);
    $insertId = Database::skip($_SESSION['game_id'], $_SESSION['last_move'], Utils::getState());
    $_SESSION['last_move'] = $insertId;
    $_SESSION['player'] = Utils::getOtherPlayerId($currentPlayer);

} catch(Exception $ex) {
    $_SESSION['error'] = $ex->getMessage();
}

header('Location: index.php');
exit();
