<?php

namespace Joyce0398\HiveGame\pieces;

use Exception;
use SplQueue;

class Spider extends AbstractPiece
{
    public function validateMove(string $from, string $to): bool
    {
        if ($this->player->getBoard()->isOccupied($to)) {
            throw new Exception('Tile is occupied');
        }
        if (!$this->isThreeStepsApart($from, $to)) {
            throw new Exception('Move is not three steps apart');
        }
        if (!$this->validateSlide($from, $to)) {
            throw new Exception('Tile has to slide');
        }
        return true;
    }

    public function validateSlide($from, $to): bool {
        if (!$this->player->getBoard()->hasNeighbour($to)) {
            return false;
        }

        return $this->canReachTile($from, $to);
    }

    private function canReachTile($from, $to): bool {
        $visited = [];
        $queue = new SplQueue();
        $queue->enqueue($from);

        while (!$queue->isEmpty()) {
            $currentTile = $queue->dequeue();

            if ($this->isVisited($currentTile, $visited)) {
                continue;
            }

            $visited[] = $currentTile;

            if ($currentTile === $to) {
                return true;
            }

            $this->enqueueNeighbours($currentTile, $visited, $queue);
        }

        return false;
    }

    private function isVisited($tile, $visited): bool {
        return in_array($tile, $visited);
    }

    private function enqueueNeighbours($tile, $visited, SplQueue $queue) {
        list($x, $y) = explode(',', $tile);

        foreach ($this->player->getBoard()::$OFFSETS as $offset) {
            $neighbour = ($x + $offset[0]) . ',' . ($y + $offset[1]);

            if (!$this->isVisited($neighbour, $visited) && !$this->player->getBoard()->isOccupied($neighbour)
                && $this->player->getBoard()->hasNeighbour($neighbour)) {
                $queue->enqueue($neighbour);
            }
        }
    }

    private function isThreeStepsApart($from, $to): bool {
        list($fromX, $fromY) = explode(',', $from);
        list($toX, $toY) = explode(',', $to);

        $xDiff = abs($toX - $fromX);
        $yDiff = abs($toY - $fromY);

        return ($xDiff + $yDiff) === 3;
    }
}
