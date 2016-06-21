<?php

class CompileCollection implements IteratorAggregate
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

    function reduce($fn, $initial = null)
    {
        $ops = $this->ops;
        $before = '$_carry = ' . var_export($initial) . ';';
        $ops[] = '$_carry = ' . $fn . ';';
        $after = '$_result = $_carry;';
        return self::evaluate($this->seed, $this->compile($ops), $before, $after);

    }

    private static function compile($ops)
    {
        return 'foreach ($_seed as $_key => $_) {'
            . implode("\n", $ops)
            . '}';
    }

    public function getIterator()
    {
        $ops = $this->ops;
        $ops[] = 'yield $_key => $_;';
        $gen = self::evaluate(
            $this->_seed,
            $this->compile($ops),
            '$_result = static function() use($_seed){',
            '};'
        );
        return $gen();
    }

    private static function evaluate($_seed, $_code, $_before, $_after)
    {
        $_result = null;
        eval("$_before \n $_code \n $_after");
        return $_result;
    }

    function sum()
    {
        $ops = $this->ops;
        $before = '$_result = 0;';
        $ops[] = '$_result += $_;';

        return self::evaluate($this->seed, $this->compile($ops), $before, '');
    }

    function product()
    {
        $ops = $this->ops;
        $before = '$_result = 1;';
        $ops[] = '$_result *= $_;';

        return self::evaluate($this->seed, $this->compile($ops), $before, '');
    }
}
