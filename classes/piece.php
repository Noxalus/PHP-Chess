<?php

class Piece
{
    protected $position;
    protected $color;
    protected $possibleCells;
    protected $history;
    
    protected function __construct($x, $y, $color)
    {
        $this->position = new Position($x, $y);
        $this->color = $color;
        $this->possibleCells = array();
        $this->history = array();
    }
    
    public function GetX()
    {
        return $this->position->x;
    }
    
    public function GetY()
    {
        return $this->position->y;
    }
    
    public function GetColor()
    {
        return $this->color;
    }
    
    public function ComputePossibleCells($collisionBoard)
    {
        $this->possibleCells = array();
    }
    
    public function GetPossibleCells()
    {
        return $this->possibleCells;
    }
    
    public function SetPosition($position)
    {
        $this->history[] = $this->position;
        $this->position = $position;
    }
    
    public function __toString()
    {
    }
}