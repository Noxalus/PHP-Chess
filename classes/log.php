<?php

class Log
{
    private $messages;
            
    public function __construct()
    {
        $this->messages = array();
        $this->messages[] = '[' . date('Y/m/d h:i:s') . ']---------- Game start ----------';
    }
    
    public function Add($message, $date = true)
    {
        $string = '';
        if ($date)
            $string .= '[' . date('Y/m/d h:i:s') . '] ';
        
        $string .= $message;
        
        $this->messages[] = $string;
    }
    
    public function Display()
    {
        $counter = 0;
        foreach($this->messages as $message)
        {
            echo '[' . $counter . ']' . $message . '<br />';
            $counter++;
        }
    }
    
    public function Clear()
    {
        $this->messages = array();
    }
}