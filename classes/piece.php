<?php

class Piece
{
    protected $x;
    protected $y;
    protected $color;
    
    protected function __construct($x, $y, $color)
    {
        $this->x = $x;
        $this->y = $y;
        $this->color = $color;
    }
    
    public function GetX()
    {
        return $x;
    }
    
    public function GetY()
    {
        return $y;
    }
    
    protected function CanMove()
    {   
    }
    
    public function Draw()
    {
    }
}