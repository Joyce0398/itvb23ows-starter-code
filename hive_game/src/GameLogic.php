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
            throw new Exception("Board position has opposing neighbour");
        }
    }

    public function checkPlay(Player $player, $piece, $to): void
    {
        $hand = $player->getHand();
        $this->checkPlayBoard($player, $to);
        if (!$hand->hasPiece($piece)) {
            throw new Exception("Player does not have the tile");
        } elseif ($hand->handSize() <= 8 && $hand->hasPiece('Q')) {
            throw new Exception('Must play queen bee');
        }
    }

    public function checkMove($player, $to, $from, $board): array
    {
        $tile = null;
        try {
            if (!$board->isOccupied($from)) {
                throw new \Exception('Board position is empty');
            } elseif (!$player->hasTile($from)) {
                throw new Exception("Tile is not owned by player");
            } elseif ($player->getHand()->hasPiece('Q')) {
                throw new Exception("Queen bee is not played");
            } else {
                $tile = $board->popTile($from);
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
                } else {
                    if ($from == $to) {
                        throw new Exception('Tile must move');
                    } elseif ($board->isOccupied($to) && $tile[1] != "B") {
                        throw new Exception('Tile not empty');
                    } elseif ($tile[1] == "Q" || $tile[1] == "B") {
                        if (!$board->slide($from, $to)) {
                            throw new Exception('Tile must slide');
                        }
                    }
                }
            }
        } catch (Exception $e) {
            if($tile) {
                if ($board->isOccupied($from)) {
                    $board->pushTile($from, $tile[1], $tile[0]);
                } else {
                    $board[$from] = [$tile];
                }
            }

            throw $e;
        }
        return $tile;
    }
}