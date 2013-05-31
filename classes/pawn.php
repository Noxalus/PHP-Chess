<?php

require_once 'piece.php';

class Pawn extends Piece
{
    public function __construct($x, $y, $color)
    {
        parent::__construct($x, $y, $color);
    }

    public function Draw()
    {
        echo '<img src="sprites/' . $this->color . '_pawn.png" />';
    }

    public function CanMove()
    {
        
    }
}