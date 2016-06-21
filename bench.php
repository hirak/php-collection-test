<?php
const LOOP = 100;
const RANGEMAX = 10000;
define('EXAMPLE_ARR', range(0, RANGEMAX));

function bench($name, callable $fn) {
    echo "$name\t";
    $start = microtime(true);
    for ($i = 0; $i < LOOP; ++$i) {
        $fn();
    }
    echo microtime(true) - $start, PHP_EOL;
}

require_once __DIR__ . '/array.php';
require_once __DIR__ . '/lazy.php';
require_once __DIR__ . '/compile.php';

bench('array', function(){
    (new Collection(EXAMPLE_ARR))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
});

bench('lazy', function(){
    (new LazyCollection(new ArrayIterator(EXAMPLE_ARR)))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
});

bench('compile', function(){
    (new CompileCollection(EXAMPLE_ARR))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
});

bench('plain', function(){
    $sum = 0;
    foreach (EXAMPLE_ARR as $v) {
        if ($v % 2) continue;
        $v **= 2;
        if ($v <= 20) continue;

        $sum += $v;
    }

    // echo $sum;
});

bench('origin', function(){
    array_sum(
        array_filter(
            array_map(
                function ($v) {
                    return $v ** 2;
                },
                array_filter(EXAMPLE_ARR, function ($v) {
                    return $v % 2 === 0;
                })
            ),
            function ($v) {
                return $v > 20;
            }
        )
    );
});
