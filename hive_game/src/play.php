<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\BoardGame;
use Joyce0398\HiveGame\Database;
use Joyce0398\HiveGame\GameLogic;
use Joyce0398\HiveGame\Hand;
use Joyce0398\HiveGame\Player;
use Joyce0398\HiveGame\Utils;

$piece = $_POST['piece'];
$to = $_POST['to'];

$gameId = $_SESSION['game_id'];
$hands = $_SESSION['hand'];
$lastMove = $_SESSION['last_move'] ?? null;

[$board, $players] = Utils::createBoardAndPlayersFromSession($_SESSION);
$currentPlayer = $players[$_SESSION['player']];

try {
    $gameLogic = new GameLogic($board);
    $gameLogic->checkPlay($currentPlayer, $piece, $to);

    $board->setTile($to, $piece, $currentPlayer->getId());
    $currentPlayer->getHand()->removePiece($piece);

    $insertId = Database::play($gameId, $piece, $to, $lastMove, Utils::getState());

    $_SESSION['hand'] = [$players[0]->getHand()->toArray(), $players[1]->getHand()->toArray()];
    $_SESSION['last_move'] = $insertId;
    $_SESSION['board'] = $board->getBoard();
    $_SESSION['player'] = Utils::getOtherPlayerId($currentPlayer);
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
