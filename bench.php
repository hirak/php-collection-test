<?php
const LOOP = 10;
const RANGEMAX = 100000;

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
    (new Collection(range(0, RANGEMAX)))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
});

bench('lazy', function(){
    (LazyCollection::range(0, RANGEMAX))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
});

bench('compile', function(){
    (new CompileCollection(range(0, RANGEMAX)))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
});

bench('plain', function(){
    $mapped = [];
    for ($v = 0; $v <= RANGEMAX; ++$v) {
        if ($v % 2) continue;
        $v **= 2;
        if ($v <= 20) continue;

        $mapped[] = $v;
    }

    array_sum($mapped);
});

bench('plain+array', function(){
    array_sum(
        array_filter(
            array_map(
                function ($v) {
                    return $v ** 2;
                },
                array_filter(range(0, 10000), function ($v) {
                    return $v % 2 === 0;
                })
            ),
            function ($v) {
                return $v > 20;
            }
        )
    );
});
