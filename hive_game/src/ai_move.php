<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\AI;
//use Joyce0398\HiveGame\BoardGame;
use Joyce0398\HiveGame\Database;
//use Joyce0398\HiveGame\GameLogic;
//use Joyce0398\HiveGame\Hand;
//use Joyce0398\HiveGame\Player;
use Joyce0398\HiveGame\Utils;


$gameId = $_SESSION['game_id'];
$hands = $_SESSION['hand'];

[$board, $players] = Utils::createBoardAndPlayersFromSession($_SESSION);
$currentPlayer = $players[$_SESSION['player']];

unset($_SESSION['error']);

try {
    $moves = Database::getMoves($gameId);
    $ai = new AI(count($moves->fetch_all(MYSQLI_ASSOC)));
    [$moveType, $first, $to] = $ai->aiMove($board, $hands);

    if($moveType == 'play') {
        echo "<form id='gameForm' action='play.php' method='post' style='display:none;'>";
        echo "<input type='hidden' name='piece' value='" . htmlspecialchars($first) . "'>";
        echo "<input type='hidden' name='to' value='" . htmlspecialchars($to) . "'>";
    } elseif($moveType == 'move') {
        echo "<form id='gameForm' action='move.php' method='post' style='display:none;'>";
        echo "<input type='hidden' name='from' value='" . htmlspecialchars($first) . "'>";
        echo "<input type='hidden' name='to' value='" . htmlspecialchars($to) . "'>";
    } else {
        echo "<form id='gameForm' action='move.php' method='post' style='display:none;'>";
    }
    echo "</form>";
    echo "<script type='text/javascript'>";
    echo "document.getElementById('gameForm').submit();";
    echo "</script>";


} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
