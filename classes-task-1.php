<?php
class Transport {
    protected int $speed;
    protected string $name;

    public function setSpeed($speed){
        $this->speed = $speed;
    }

    public function __toString(){
        return $this->name . ": " . $this->speed;
    }
}

class Bus extends Transport{
    public function __construct(){
        $this->name = "Bus";
    }
}

class Car extends Transport{
    public function __construct(){
        $this->name = "Car";
    }
}

class Bike extends Transport{
    public function __construct(){
        $this->name = "Bike";
    }
}