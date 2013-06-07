<?php

class Log
{
    private $messages;
            
    public function __construct()
    {
        $this->messages = array();
    }
    
    public function Add($message)
    {
        $this->messages[] = '[' . date('Y/m/d h:i:s') . '] ' . $message;
    }
    
    public function Display()
    {
        foreach($this->messages as $message)
        {
            echo $message . '<br />';
        }
    }
    
    public function Clear()
    {
        $this->messages = array();
    }
}