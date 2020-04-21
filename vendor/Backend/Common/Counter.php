<?php
namespace Backend\Common;

class Counter
{
    protected int $counter = 0;

    public function __construct()
    {
        $this->counter = 0;
    }

    public function next() : int
    {
        return ++$this->counter;
    }

    public function current() : int
    {
        return $this->counter;
    }
}
