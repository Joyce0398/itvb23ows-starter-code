<?php

namespace Joyce0398\HiveGame;

use Exception;

class Grasshopper
{
    private Player $player;
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function validateMove(string $from, string $to): bool
    {
        if ($this->player->getBoard()->isOccupied($to)) {
            throw new Exception('Tile not empty');
        }
        if (!$this->checkJumpTiles($from, $to)) {
            throw new Exception('Did not follow jump rules');
        }
        if (!$this->isHorizontalOrVertical($from, $to)) {
            throw new Exception('Move is not horizontal or vertical');
        }
        return true;
    }

    private function isHorizontalOrVertical(string $from, string $to): bool
    {
        [$fromX, $fromY] = explode(',', $from);
        [$toX, $toY] = explode(',', $to);

        if ($fromX == $toX || $fromY == $toY) {
            return true;
        }

        if (abs($fromX - $toX) == abs($fromY - $toY)) {
            return true;
        }

        return false;
    }


    private function checkJumpTiles(string $from, string $to): bool
    {
        [$startX, $startY] = explode(',', $from);
        [$endX, $endY] = explode(',', $to);

        $tiles = [];
        if ($startY == $endY) {
            for ($x = min($startX, $endX) + 1; $x < max($startX, $endX); $x++) {
                $tiles[] = "$x,$startY";
            }
        } else {
            for ($y = min($startY, $endY) + 1; $y < max($startY, $endY); $y++) {
                $tiles[] = "$startX,$y";
            }
        }

        if (count($tiles) <= 1) {
            return false;
        }
        return array_reduce($tiles, function ($carry, $pos) {
            return $carry && $this->player->getBoard()->isOccupied($pos);
        }, true);
    }
}