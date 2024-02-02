<?php
namespace Joyce0398\HiveGame;

class Utils {

    public static function getOtherPlayerId(Player $currentPlayer): int
    {
        return 1 - $currentPlayer->getId();
    }

    public static function createBoardAndPlayersFromSession(array $session)
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
}