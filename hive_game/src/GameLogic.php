<?php

namespace Joyce0398\HiveGame;

use Exception;
use Joyce0398\HiveGame\pieces\Grasshopper;
use Joyce0398\HiveGame\pieces\SoldierAnt;
use Joyce0398\HiveGame\pieces\Spider;

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
//            $pieces = $this->board->getPlayedPieces();
//            if(count($pieces) > 1 || !isset($pieces['Q'])) {
            throw new Exception("Board position has opposing neighbour");
//            }
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
                throw new Exception('Board position is empty');
            } elseif (!$player->hasTile($from)) {
                throw new Exception("Tile is not owned by player");
            } elseif ($player->getHand()->hasPiece('Q')) {
                throw new Exception("Queen bee is not played");
            }
            $tile = $board->popTile($from);
            $availablePieces = array_keys($player->getHand()->getAvailablePieces());

            $this->validateHiveSplit($to);

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

    public function validateMove(Player $player, $to, $from, $piece) {
        $tile = $this->checkMove($player, $to, $from);
        if ($tile) {
            if ($this->board->isOccupied($from)) {
                $this->board->pushTile($from, $tile[1], $tile[0]);
            } else {
                $this->board->setTile($from, $tile[1], $tile[0]);
            }
        }
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
        elseif($tile[1] == 'S') {
            $spider = new Spider($player);
            $spider->validateMove($from, $to);
        }
        elseif($tile[1] == 'A') {
            $ant = new SoldierAnt($player);
            $ant->validateMove($from, $to);
        }
    }

    public function getValidPositionsPlay(Player $player): array
    {
        $to = [];
        $offsets = $this->board::$OFFSETS;
        $positions = array_keys($this->board->getBoard());
        foreach ($offsets as $pq) {
            foreach ($positions as $pos) {
                $pq2 = explode(',', $pos);
                $result = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
                try {
                    $this->checkPlayBoard($player, $result);
                } catch (Exception $ex) {
                    continue;
                }
                $to[] = $result;
            }
        }
        return array_unique($to);
    }

    public function getValidPositionsMove(Player $player): array
    {
        $from = $this->board->getPlayerTiles($player);
        $pieces = $this->board->getPlayerPieces($player);

        $to = [];
        $offsets = $this->board::$OFFSETS;

        $positions = array_keys($this->board->getBoard());

        foreach ($pieces as $piece) {
            foreach ($from as $fromTile) {
                foreach ($offsets as $pq) {
                    foreach ($positions as $pos) {
                        $pq2 = explode(',', $pos);
                        $toCoordinate = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
                        try {
                            $this->validateMove($player, $toCoordinate, $fromTile, $piece);
                            $to[] = $toCoordinate;
                        } catch (Exception $ex) {
                            continue;
                        }
                    }
                }
            }
        }
        return array_unique($to);
    }


    public function undo($lastMove)
    {
        if(empty($this->board->getBoard())) {
            throw new Exception('Cant undo');
        }

        $result = Database::getMove($lastMove);
        Database::deleteMove($result['id']);;

        Utils::setState($result['state']);

        return $result['previous_id'];
    }

    public function checkSkip(Player $player)
    {
        $pieces = $player->getHand()->getAvailablePieces();
        if(!empty($pieces))
        {
            $plays = $this->getValidPositionsPlay($player);
        }

        $moves = $this->getValidPositionsMove($player);
        if(!empty($plays) || !empty($moves))
        {
            throw new Exception('You can still play a piece or move');
        }
    }

    public function playerHasWon(Player $player): bool
    {
        $queenPiece = $this->board->findPiece('Q', $player);

        if($queenPiece) {
            $origin = explode(',', $queenPiece);
            $count = 0;
            foreach ($this->board::$OFFSETS as $offset) {
                $neighbour = $origin[0] + $offset[0] . (',' . ($origin[1] + $offset[1]));
                if ($this->board->isOccupied($neighbour)) {
                    $count++;
                }
            }

            if ($count == 6) {
                return true;
            }
        }
        return false;
    }
}
