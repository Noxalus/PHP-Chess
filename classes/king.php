<?php

require_once 'piece.php';

class King extends Piece
{
    private $check;
    private $checkAtLeastOnce;
    public function __construct($x, $y, $color)
    {
        parent::__construct($x, $y, $color);
        
        $this->check = false;
        $this->checkAtLeastOnce = false;
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
                    $this->possibleCells[] = new Position($x, $y);
                }
            }
        }
    }
    
    public function InCheck()
    {
        return $this->check;
    }
    
    public function CheckAtLeastOnce()
    {
        return $this->checkAtLeastOnce;
    }
    
    public function Check($bool)
    {
        if ($bool && !$this->checkAtLeastOnce)
            $this->checkAtLeastOnce = true;
        
        $this->check = $bool;
    }
    
    public function __toString()
    {
        return '<img src="sprites/' . $this->color . '_king.png" class="piece" />';
    }
}