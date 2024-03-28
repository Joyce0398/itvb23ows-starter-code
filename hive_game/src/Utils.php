<?php
namespace Joyce0398\HiveGame;

class Utils {

    public static function getOtherPlayerId(Player $currentPlayer): int
    {
        return 1 - $currentPlayer->getId();
    }

    public static function createBoardAndPlayersFromSession(array $session): array
    {
        $board = new BoardGame($session['board'] ?? []);
        if(isset($session['hand'])) {
            $hands = [new Hand($session['hand'][0]), new Hand($session['hand'][1])];
        } else {
            $hands = [new Hand(), new Hand()];
        }
        $players = [
            new Player(0, $board, $hands[0]),
            new Player(1, $board, $hands[1])
        ];
        return [$board, $players];
    }

    public static function getState()
    {
        // alleen SESSION hier en in play en index
        return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
    }

    public static function setState($state)
    {
        list($_SESSION['hand'], $_SESSION['board'], $_SESSION['player']) = unserialize($state);
    }
}
