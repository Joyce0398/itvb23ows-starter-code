<?php

namespace Joyce0398\HiveGame;

use Exception;

class BoardGame
{
    public static $OFFSETS = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];
    public array $board;

    public function __construct(array $board = [])
    {
        $this->board = $board;
    }

    public static function getOffsets()
    {
        return self::$OFFSETS;
    }

    public function getBoard()
    {
        return $this->board;
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

    public function isEmpty()
    {
        return count($this->board) === 0;
    }

    public function getKeys(): array
    {
        return array_keys($this->board);
    }

    public function popTile(string $position): array
    {
        $tile = array_pop($this->board[$position]);

        if (empty($this->board[$position])) {
            unset($this->board[$position]);
        }

        return $tile;
    }

    public function getTile(string $position)
    {
        return $this->board[$position];
    }

    public function getOccupiedTiles()
    {
        return array_filter($this->board, function ($tileStack) {
            return !empty($tileStack);
        });
    }

    public function isPlayerOccupying($from, $player)
    {
        if (isset($this->board[$from]) && count($this->board[$from]) > 0) {
            return $this->board[$from][count($this->board[$from]) - 1][0] == $player;
        }

        return false;
    }

    public function isNeighbour($a, $b)
    {
        $a = explode(',', $a);
        $b = explode(',', $b);

        if (($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) || ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1)) {
            return true;
        }

        if ($a[0] + $a[1] == $b[0] + $b[1]) {
            return true;
        }

        return false;
    }

    public function hasNeighbour($a)
    {
        foreach (array_keys($this->board) as $b) {
            if ($this->isNeighbour($a, $b)) {
                return true;
            }
        }
        return false;
    }

    public function neighboursAreSameColor($player, $a)
    {
        foreach ($this->board as $b => $st) {
            if (!$st) {
                continue;
            }
            $c = $st[count($st) - 1][0];
            if ($c != $player && $this->isNeighbour($a, $b)) {
                return false;
            }
        }
        return true;
    }

    public function isOccupied(string $position): bool
    {
        return isset($this->board[$position]);
    }

    public function len($tile)
    {
        return $tile ? count($tile) : 0;
    }

    public function pushTile(string $position, string $piece, int $player)
    {
        array_push($this->board[$position], array($player, $piece));
    }

    public function setTile(string $position, string $piece, int $player)
    {
        $this->board[$position] = [[$player, $piece]];
    }

    public function slide(string $from, string $to): bool
    {
        if (!$this->hasNeighbour($to) || !$this->isNeighbour($from, $to)) {
            return false;
        }
        $b = explode(',', $to);
        $common = [];
        foreach (self::$OFFSETS as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->isNeighbour($from, $p . "," . $q)) {
                $common[] = $p . "," . $q;
            }
        }

        if (count($this->board) == 2 && !$this->isOccupied($common[0]) && !$this->isOccupied($common[0])) {
            return false;
        }
        return true;
    }

    public function getPlayedPieces() {
        $result = [];
        foreach ($this->board as $subArray) {
            foreach ($subArray as $piece) {
                $pieceType = $piece[1];
                if (isset($result[$pieceType])) {
                    $result[$pieceType]++;
                } else {
                    $result[$pieceType] = 1;
                }
            }
        }
        return $result;
    }
}
