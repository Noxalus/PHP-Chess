<?php

require_once 'piece.php';

class Knight extends Piece
{
    public function __construct($x, $y, $color)
    {
        parent::__construct($x, $y, $color);
    }

    public function ComputePossibleCells($board)
    {
        parent::ComputePossibleCells($board);
     
        $collisionBoard = $board->ComputeCollisionBoard($this->color);

        $positions = array(
            new Position($this->position->x + 1, $this->position->y + 2),
            new Position($this->position->x + 2, $this->position->y + 1),
            new Position($this->position->x + 2, $this->position->y - 1),
            new Position($this->position->x + 1, $this->position->y - 2),
            new Position($this->position->x - 1, $this->position->y - 2),
            new Position($this->position->x - 2, $this->position->y - 1),
            new Position($this->position->x - 2, $this->position->y + 1),
            new Position($this->position->x - 1, $this->position->y + 2),
        );
        
        foreach($positions as $position)
        {
            if (!Board::Out($position) && !$collisionBoard[$position->x][$position->y])
            {
                $this->possibleCells[] = $position;
            }
        }
    }
    
    public function __toString()
    {
        return '<img src="sprites/' . $this->color . '_knight.png" class="piece" />';
    }
}