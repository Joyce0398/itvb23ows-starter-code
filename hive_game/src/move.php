<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\BoardGame;
use Joyce0398\HiveGame\Database;
use Joyce0398\HiveGame\GameLogic;
use Joyce0398\HiveGame\Hand;
use Joyce0398\HiveGame\Player;
use Joyce0398\HiveGame\Utils;

$from = $_POST['from'];
$to = $_POST['to'];


$gameId = $_SESSION['game_id'];
$hands = $_SESSION['hand'];
$lastMove = $_SESSION['last_move'];

[$board, $players] = Utils::createBoardAndPlayersFromSession($_SESSION);
$currentPlayer = $players[$_SESSION['player']];

unset($_SESSION['error']);

try {
    $gameLogic = new GameLogic($board);
    $tile = $gameLogic->checkMove($currentPlayer, $to, $from);

    if ($board->isOccupied($to)) {
        $board->pushTile($to, $tile[1], $tile[0]);
    } else {
        $board->setTile($to, $tile[1], $tile[0]);
    }

    $insertId = Database::move($gameId, $from, $to, $lastMove, BoardGame::getState());

    $_SESSION['player'] = Utils::getOtherPlayerId($currentPlayer);
    $_SESSION['last_move'] = $insertId;
    $_SESSION['board'] = $board->getBoard();

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
