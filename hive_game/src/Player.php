<?php

namespace Joyce0398\HiveGame;


use Exception;

class Player
{
    private Hand $hand;
    private int $player;
    private BoardGame $board;

    public function __construct(int $player, BoardGame $board, Hand $hand)
    {
        $this->player = $player;
        $this->board = $board;
        $this->hand = $hand;
    }

    public function getAvailablePositions()
    {
        $board = $this->board;
        $gameLogic = new GameLogic($board);
        $to = [];
        foreach ($board::$OFFSETS as $pq) {
            foreach ($board->getKeys() as $pos) {
                $pq2 = explode(',', $pos);
                try {
                    $gameLogic->checkPlayBoard($this, ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]));
                } catch(Exception $ex) {continue;}
                $to[] = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
            }
        }

        return array_unique($to);
    }

    public function getOwnedTiles() {
        $playerId = $this->player;

        $tiles = array_filter($this->board->getBoard(), function ($row) use ($playerId) {
            return is_array($row) && isset($row[0]) && is_array($row[0]) && $row[0][0] == $playerId;
        });

        return array_keys($tiles);
    }

    public function hasTile(string $coordinate)
    {
        return $this->board->isPlayerOccupying($coordinate, $this->player);
    }

    public function getHand()
    {
        return $this->hand;
    }

    public function getBoard(): BoardGame
    {
        return $this->board;
    }

    public function getId(): int
    {
        return $this->player;
    }
}
