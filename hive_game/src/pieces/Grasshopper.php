<?php

namespace Joyce0398\HiveGame\pieces;

use Exception;

class Grasshopper extends AbstractPiece
{
    public function validateMove(string $from, string $to): bool
    {
        if ($this->player->getBoard()->isOccupied($to)) {
            throw new Exception('Tile not empty');
        }
        if (!$this->checkJumpTiles($from, $to)) {
            throw new Exception('Grasshopper needs to move at least 1 tile');
        }
        if (!$this->isStraight($from, $to)) {
            throw new Exception('Move is not straight');
        }
        if (!$this->noEmptyPositions($from, $to)) {
            throw new Exception('Grasshopper cant jump over empty positions');
        }
        return true;
    }

    private function isStraight(string $from, string $to): bool
    {
        return $this->isOnSameAxis($from, $to) || $this->isOnDiagonal($from, $to);
    }

    private function isOnSameAxis(string $from, string $to): bool
    {
        [$fromX, $fromY] = explode(',', $from);
        [$toX, $toY] = explode(',', $to);

        return $fromX == $toX || $fromY == $toY;
    }

    private function isOnDiagonal(string $from, string $to): bool
    {
        [$fromX, $fromY] = explode(',', $from);
        [$toX, $toY] = explode(',', $to);

        return abs($fromX - $toX) == abs($fromY - $toY);
    }

    private function noEmptyPositions(string $from, string $to): bool
    {
        [$startX, $startY] = explode(',', $from);
        [$endX, $endY] = explode(',', $to);

        $positions = $this->isHorizontalMove($startY, $endY) ?
            $this->generateCoordinatesHorizontal($startX, $endX, $startY) :
            $this->generateCoordinatesVertical($startY, $endY, $startX);

        foreach ($positions as $pos) {
            if (!$this->player->getBoard()->isOccupied($pos)) {
                return false;
            }
        }

        return true;
    }

    private function isHorizontalMove(int $y1, int $y2): bool
    {
        return $y1 == $y2;
    }

    private function generateCoordinatesHorizontal(int $startX, int $endX, int $y): array
    {
        return $this->generateCoordinates($startX, $endX, $y, true);
    }

    private function generateCoordinatesVertical(int $startY, int $endY, int $x): array
    {
        return $this->generateCoordinates($startY, $endY, $x, false);
    }

    private function generateCoordinates(int $start, int $end, int $fixed, bool $isHorizontal): array
    {
        $coordinates = [];
        for ($i = min($start, $end) + 1; $i < max($start, $end); $i++) {
            $coordinates[] = $isHorizontal ? "$i,$fixed" : "$fixed,$i";
        }
        return $coordinates;
    }

    private function checkJumpTiles(string $from, string $to): bool
    {
        [$startX, $startY] = explode(',', $from);
        [$endX, $endY] = explode(',', $to);

        $positions = $this->isHorizontalMove($startY, $endY) ?
            $this->generateCoordinatesHorizontal($startX, $endX, $startY) :
            $this->generateCoordinatesVertical($startY, $endY, $startX);

        return count($positions) > 0;
    }
}
