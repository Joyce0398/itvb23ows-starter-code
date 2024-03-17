<?php
    
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\Database;
use Joyce0398\HiveGame\GameLogic;
use Joyce0398\HiveGame\Utils;

if (!isset($_SESSION['board'])) {
        header('Location: restart.php');
        exit(0);
    }

$gameId = $_SESSION['game_id'];

[$board, $players] = Utils::createBoardAndPlayersFromSession($_SESSION);
$currentPlayer = $players[$_SESSION['player']];
$gameLogic = new GameLogic($board);
$availablePieces = $currentPlayer->getHand()->getAvailablePieces();

$to = $currentPlayer->getAvailablePositions();
if (empty($to)) {
    $to[] = '0,0';
}
$moves = $gameLogic->getValidPositionsMove($currentPlayer);

$playerOneWon = $gameLogic->playerHasWon($players[0]);
$playerTwoWon = $gameLogic->playerHasWon($players[0]);
$gameState = '';
if ($playerOneWon && $playerTwoWon) {
    $gameState = 'Game ended in a draw';
} elseif ($playerOneWon) {
    $gameState = 'Black has won';
} elseif ($playerTwoWon) {
    $gameState = 'White has won';
}


?>
<!DOCTYPE html>
<html lang="EN" xml:lang="en">
    <head>
        <title>Hive</title>
        <style>
            div.board {
                width: 60%;
                height: 100%;
                min-height: 500px;
                float: left;
                overflow: scroll;
                position: relative;
            }

            div.board div.tile {
                position: absolute;
            }

            div.tile {
                display: inline-block;
                width: 4em;
                height: 4em;
                border: 1px solid black;
                box-sizing: border-box;
                font-size: 50%;
                padding: 2px;
            }

            div.tile span {
                display: block;
                width: 100%;
                text-align: center;
                font-size: 200%;
            }

            div.player0 {
                color: black;
                background: white;
            }

            div.player1 {
                color: white;
                background: black
            }

            div.stacked {
                border-width: 3px;
                border-color: red;
                padding: 0;
            }
        </style>
    </head>
    <body>
        <div class="board">
            <?php
                $min_p = 1000;
                $min_q = 1000;
                foreach ($board->getBoard() as $pos => $tile) {
                    $pq = explode(',', $pos);
                    if ($pq[0] < $min_p) { $min_p = $pq[0]; }
                    if ($pq[1] < $min_q) { $min_q = $pq[1]; }
                }
                foreach ($board->getOccupiedTiles() as $pos => $tile) {
                    $pq = explode(',', $pos);
                    $pq[0];
                    $pq[1];
                    $h = count($tile);
                    echo '<div class="tile player';
                    echo $tile[$h-1][0];
                    if ($h > 1) { echo ' stacked'; }
                    echo '" style="left: ';
                    echo ($pq[0] - $min_p) * 4 + ($pq[1] - $min_q) * 2;
                    echo 'em; top: ';
                    echo ($pq[1] - $min_q) * 4;
                    echo "em;\">($pq[0],$pq[1])<span>";
                    echo $tile[$h-1][1];
                    echo '</span></div>';
                }
            ?>
        </div>
        <h1>
            <?php
            echo $gameState
            ?>
        </h1>
        <div class="hand">
            White:
            <?php
                foreach($players[0]->getHand()->toArray() as $tile => $ct) {
                    for ($i = 0; $i < $ct; $i++) {
                        echo '<div class="tile player0"><span>'.$tile."</span></div> ";
                    }
                }
            ?>
        </div>
        <div class="hand">
            Black:
            <?php
                foreach ($players[1]->getHand()->toArray() as $tile => $ct) {
                    for ($i = 0; $i < $ct; $i++) {
                        echo '<div class="tile player1"><span>'.$tile."</span></div> ";
                    }
                }
            ?>
        </div>
        <div class="turn">
            Turn: <?php if ($currentPlayer->getId() == 0){ echo "White"; } else { echo "Black"; } ?>
        </div>
        <form method="post" action="play.php">
            <select name="piece">
                <?php
                    foreach ($availablePieces as $tile => $ct) {
                        echo "<option value=\"$tile\">$tile</option>";
                    }
                ?>
            </select>
            <select name="to">
                <?php
                    foreach ($to as $pos) {
                        echo "<option value=\"$pos\">$pos</option>";
                    }
                ?>
            </select>
            <input type="submit" value="Play">
        </form>
        <form method="post" action="move.php">
            <select name="from">
                <?php
                    foreach ($currentPlayer->getOwnedTiles() as $pos) {
                        echo "<option value=\"$pos\">$pos</option>";
                    }
                ?>
            </select>
            <select name="to">
                <?php
                    foreach ($moves as $pos) {
                        echo "<option value=\"$pos\">$pos</option>";
                    }
                ?>
            </select>
            <input type="submit" value="Move">
        </form>
        <form method="post" action="pass.php">
            <input type="submit" value="Pass">
        </form>
        <form method="post" action="restart.php">
            <input type="submit" value="Restart">
        </form>
        <strong><?php if (isset($_SESSION['error'])) { echo $_SESSION['error']; } unset($_SESSION['error']); ?></strong>
        <ol>
            <?php
                $result = Database::getMoves($gameId);
                while ($row = $result->fetch_array()) {
                    echo '<li>'.$row[2].' '.$row[3].' '.$row[4].'</li>';
                }
            ?>
        </ol>
        <form method="post" action="undo.php">
            <input type="submit" value="Undo">
        </form>
    </body>
</html>
