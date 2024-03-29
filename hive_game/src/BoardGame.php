<?php

namespace Joyce0398\HiveGame;

class BoardGame
{
    public static array $offsets = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];
    public array $board;

    public function __construct(array $board = [])
    {
        $this->board = $board;
    }

    public static function getOffsets(): array
    {
        return self::$offsets;
    }

    public function getBoard(): array
    {
        return $this->board;
    }

    public function isEmpty(): bool
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

    public function getOccupiedTiles(): array
    {
        return array_filter($this->board, function ($tileStack) {
            return !empty($tileStack);
        });
    }

    public function findPiece(string $piece, Player $player)
    {
        foreach ($this->board as $tile => $tileData)
        {
            [$playerTile, $pieceTile] = end($tileData);
            if ($playerTile == $player->getId() && $pieceTile == $piece)
            {
                return $tile;
            }
        }
        return null;
    }

    public function isPlayerOccupying($from, $player): bool
    {
        if (isset($this->board[$from]) && count($this->board[$from]) > 0) {
            return $this->board[$from][count($this->board[$from]) - 1][0] == $player;
        }

        return false;
    }

    public function isNeighbour($a, $b): bool
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

    public function hasNeighbour($a): bool
    {
        foreach (array_keys($this->board) as $b) {
            if ($this->isNeighbour($a, $b)) {
                return true;
            }
        }
        return false;
    }

    public function neighboursAreSameColor($player, $a): bool
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

    public function pushTile(string $position, string $piece, int $player)
    {
        $this->board[$position][] = array($player, $piece);
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
        foreach (self::$offsets as $pq) {
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

    public function getPlayerTiles(Player $player): array
    {
        $tiles = array_filter($this->board, function ($value) use ($player) {
            return is_array($value) && isset($value[0]) && is_array($value[0]) && $value[0][0] == $player->getId();
        });
        return array_keys($tiles);
    }

    public function getPlayerPieces(Player $player): array
    {
        $tiles = $this->getPlayerTiles($player);

        $pieces = [];
        foreach($tiles as $tile) {
            $tileOnTop = end($this->board[$tile]);
            $piece = $tileOnTop[1];
            $pieces[] = $piece;
        }
        return $pieces;
    }
}
