<?php

/**
 * Class to represent a player color
 */
class Color
{
    const White = 0;
    const Black = 1;
    
    public static function ColorToString($color)
    {
        return ($color === Color::White) ? 'white' : 'black';
    }
    
    public static function Invert($color)
    {
        return ($color === Color::White) ? 1 : 0;
    }
    
    public static function Factor($color)
    {
        return ($color === Color::White) ? 1 : -1;
    }
}