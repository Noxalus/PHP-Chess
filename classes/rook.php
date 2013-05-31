<?php

require_once 'piece.php';

class Rook extends Piece
{
    public function __construct($x, $y, $color)
    {
        parent::__construct($x, $y, $color);
    }

    public function Draw()
    {
        echo '<img src="sprites/' . $this->color . '_rook.png" />';
    }

    public function CanMove()
    {
        
    }
}