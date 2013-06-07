<?php

class Color
{
    const White = 0;
    const Black = 1;
    
    public static function ColorToString($color)
    {
        return ($color === 0) ? 'white' : 'black';
    }
}