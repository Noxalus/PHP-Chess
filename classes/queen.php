<?php

require_once 'piece.php';

class Queen extends Piece
{
    public function __construct($x, $y, $color)
    {
        parent::__construct($x, $y, $color);
    }

    public function __toString()
    {
        return '<img src="sprites/' . $this->color . '_queen.png" />';
    }

    public function ComputePossibleCells($collisionBoard)
    {
        parent::ComputePossibleCells($collisionBoard);
                
        for ($y = 7; $y >= 0; $y--)
        {
            for ($x = 7; $x >= 0; $x--)
            {
                if ($collisionBoard[$x][$y])
                    echo 1;
                else
                    echo 0;
            }
            
            echo '<br />';
        }
        
        for($x = $this->position->x + 1; $x < 8; $x++)
        {
            if (!$collisionBoard[$x][$this->position->y])
            {
                $this->possibleCells[] = new Position($x, $this->position->y);
            }
            else
                break;
        }
        
        for($x = $this->position->x + 1; $x >= 0; $x--)
        {
            if (!$collisionBoard[$x][$this->position->y])
            {
                $this->possibleCells[] = new Position($x, $this->position->y);
            }
            else
                break;
        }
        
        for($y = $this->position->y + 1; $y < 8; $y++)
        {
            if (!$collisionBoard[$this->position->x][$y])
            {
                $this->possibleCells[] = new Position($this->position->x, $y);
            }
            else
                break;
        }
        
        for($y = $this->position->y - 1; $y >= 0; $y--)
        {
            if (!$collisionBoard[$this->position->x][$y])
            {
                $this->possibleCells[] = new Position($this->position->x, $y);
            }
            else
                break;
        }
        
        $position = new Position($this->position->x, $this->position->y);
        while(!Board::Out($position))
        {
            if (!$collisionBoard[$position->x][$position->y])
            {
                $this->possibleCells[] = $position;
            }
            
            $position = new Position($position->x + 1, $position->y + 1);
        }
    }
}