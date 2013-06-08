<?php

require_once 'piece.php';

class King extends Piece
{
    private $check;
    private $checkNumber;
    public function __construct($x, $y, $color)
    {
        parent::__construct($x, $y, $color);
        
        $this->check = false;
        $this->checkNumber = 0;
    }

    public function ComputePossibleCells($board)
    {
        parent::ComputePossibleCells($board);
     
        $collisionBoard = $board->ComputeCollisionBoard($this->color);

        for($x = $this->position->x - 1; $x <= $this->position->x + 1; $x++)
        {
            for($y = $this->position->y - 1; $y <= $this->position->y + 1; $y++)
            {
                if (!Board::Out(new Position($x, $y)) && !$collisionBoard[$x][$y])
                {
                    $position = new Position($x, $y);
                    
                    if (!$board->KingCheck($this->GetColor(), $position))
                        $this->possibleCells[] = $position;
                }
            }
        }
    }
    
    public function CheckAtLeastOnce()
    {
        return ($this->checkNumber > 0);
    }
    
    public function SetCheck($bool)
    {
        if ($bool)
            $this->checkNumber++;
        
        $this->check = $bool;
    }
    
    public function InCheck()
    {
        return $this->check;
    }
    
    public function __toString()
    {
        return '<img src="sprites/' . $this->color . '_king.png" class="piece" />';
    }
}