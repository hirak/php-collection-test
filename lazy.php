<?php
require_once __DIR__ . '/lambda.php';

class LazyCollection implements IteratorAggregate
{
    private $ite;

    function __construct(Iterator $ite)
    {
        $this->ite = $ite;
    }

    function getIterator()
    {
        return $this->ite;
    }

    static function range($start, $end)
    {
        $gen = function ($start, $end) {
            for ($i = $start; $i <= $end; ++$i) {
                yield $i;
            }
        };
        return new self($gen($start, $end));
    }

    function filter($fn)
    {
        if (!is_callable($fn)) {
            $fn = Lambda::create('$_', $fn, true);
        }
        $this->ite = new CallbackFilterIterator($this->ite, $fn);
        return $this;
    }

    function map($fn)
    {
        if (!is_callable($fn)) {
            $fn = Lambda::create('$_', $fn, true);
        }
        $gen = function ($ite, $fn) {
            foreach ($ite as $v) {
                yield $fn($v);
            }
        };
        $this->ite = $gen($this->ite, $fn);
        return $this;
    }

    function sum()
    {
        $sum = 0;
        foreach ($this as $v) {
            $sum += $v;
        }
        return $sum;
    }
}
