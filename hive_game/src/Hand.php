<?php
namespace Joyce0398\HiveGame;

class Hand {
    private array $hand;

    public static array $defaultHand = ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];

    public function __construct(array $hand = null){
        if($hand == null) {
            $this->hand = Hand::$defaultHand;
        } else {
        $this->hand = $hand;
        }
    }

    public function handSize() {
        return array_sum($this->hand);
    }

    public function hasPiece(string $piece): bool
    {
        return $this->hand[$piece] > 0;
    }

    public function getAvailablePieces(): array
    {
        return array_filter($this->hand);
    }

    public function removePiece(string $piece): void
    {
        $this->hand[$piece]--;
    }

    public function containsTile(string $tile): bool
    {
        return in_array($tile, $this->hand);
    }

    public function toArray(): array
    {
        return $this->hand;
    }
}
