<?php

spl_autoload_register(function ($class_name){
 
    $filename = $class_name . ".php";
    
    if(file_exists('class'.DIRECTORY_SEPARATOR.$filename)){
        require_once('class'.DIRECTORY_SEPARATOR.$filename);
    }
    
}); 
