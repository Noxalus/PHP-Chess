<?php

/**
 * Class to represent a piece position on the chess board
 */
class Position
{
	public $x;
	public $y;
        
        public function __construct($x, $y)
        {
            $this->x = $x;
            $this->y = $y;
        }
        
        public function __toString()
        {
            return '[' . chr(96 + (8 - $this->x)) . ', ' . ($this->y + 1) . ']';
        }
}