<?php
require_once __DIR__ . '/lambda.php';

class CompileCollection
{
    private $ops = [];

    private $seed;

    function __construct($seed)
    {
        $this->seed = $seed;
    }

    function filter($fn)
    {
        $this->ops[] = 'if (!(' . $fn . ')) continue;';
        return $this;
    }

    function map($fn)
    {
        $this->ops[] = '$_ = ' . $fn . ';';
        return $this;
    }

    function compile()
    {
        $codes = $this->ops;
        array_unshift($codes, 'return static function($seed) { foreach ($seed as $_) {');
        $codes[]  = 'yield $_;';
        $codes[]  = '}};';
        $code = implode("\n", $codes);

        return eval($code);
    }

    function sum()
    {
        $gen = $this->compile();
        $arr = iterator_to_array($gen($this->seed));
        return array_sum($arr);
    }
}
