<?php

namespace App\Test;

class TestClass
{
    private $property1;
    private $property2;
    
    public function __construct($param1,$param2)
    {
        $this->property1=$param1;
        $this->property2=$param2;
    }
    
    public function method1()
    {
        if(true){
            echo "test";
        }
    }
}
