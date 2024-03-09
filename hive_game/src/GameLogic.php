<?php

namespace Joyce0398\HiveGame;

use Exception;

class GameLogic
{
    public BoardGame $board;

    public function __construct(BoardGame $board)
    {
        $this->board = $board;
    }

    public function checkPlayBoard(Player $player, $to): void
    {
        $hand = $player->getHand();
        if ($this->board->isOccupied($to)) {
            throw new Exception('Board position is not empty');
        } elseif (!$this->board->isEmpty() && !$this->board->hasNeighbour($to)) {
            throw new Exception("Board position has no neighbour");
        } elseif ($hand->handSize() < 11 && !$this->board->neighboursAreSameColor($player->getId(), $to)) {
            $pieces = $this->board->getPlayedPieces();
            if(count($pieces) > 1 || !isset($pieces['Q'])) {
                throw new Exception("Board position has opposing neighbour");
            }
        }
    }

    public function checkPlay(Player $player, $piece, $to): bool
    {
        $hand = $player->getHand();
        $this->checkPlayBoard($player, $to);
        if (!$hand->hasPiece($piece)) {
            throw new Exception("Player does not have the tile");
        } elseif ($hand->handSize() <= 8 && $hand->hasPiece('Q') && $piece != 'Q') {
            throw new Exception('Must play queen bee');
        }
        return true;
    }

    private function validateHiveSplit($to)
    {
        $board = $this->board;

        if (!$board->hasNeighBour($to)) {
            throw new Exception("Move would split hive");
        }

        $all = $board->getKeys();
        $queue = [array_shift($all)];
        while ($queue) {
            $next = explode(',', array_shift($queue));
            foreach (BoardGame::getOffsets() as $pq) {
                list($p, $q) = $pq;
                $p += $next[0];
                $q += $next[1];
                if (in_array("$p,$q", $all)) {
                    $queue[] = "$p,$q";
                    $all = array_diff($all, ["$p,$q"]);
                }
            }
        }
        if ($all) {
            throw new Exception("Move would split hive");
        }
    }

    public function checkMove(Player $player, $to, $from): array
    {
        $board = $this->board;
        $tile = null;
        try {
            if (!$board->isOccupied($from)) {
                throw new \Exception('Board position is empty');
            } elseif (!$player->hasTile($from)) {
                throw new Exception("Tile is not owned by player");
            } elseif ($player->getHand()->hasPiece('Q')) {
                throw new Exception("Queen bee is not played");
            }
            $tile = $board->popTile($from);
            $availablePieces = array_keys($player->getHand()->getAvailablePieces());
            if (count($availablePieces) != 4 && !isset($availablePieces['Q'])) {
                $this->validateHiveSplit($to);
            }

            if ($from == $to) {
                throw new Exception('Tile must move');
            }

            $this->validatePieceRules($player, $from, $to, $tile);

        } catch (Exception $e) {
            if ($tile) {
                if ($board->isOccupied($from)) {
                    $board->pushTile($from, $tile[1], $tile[0]);
                } else {
                    $board->setTile($from, $tile[1], $tile[0]);
                }
            }

            throw $e;
        }
        return $tile;
    }

    public function validatePieceRules(Player $player, $from, $to, $tile)
    {
        $board = $player->getBoard();
        if($tile[1] == 'G') {
            $grasshopper = new Grasshopper($player);
            $grasshopper->validateMove($from, $to);
        }
        elseif($tile[1] == 'Q') {
            if ($board->isOccupied($to)) {
                throw new Exception('Tile not empty');
            }
            if (!$board->slide($from, $to)) {
                throw new Exception('Tile must slide');
            }
        }
        elseif($tile[1] == 'B') {
            if (!$board->slide($from, $to)) {
                throw new Exception('Tile must slide');
            }
        }
    }
}
