<?php
const LOOP = 100;

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
    (new Collection(range(0, 10000)))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
});

bench('lazy', function(){
    (LazyCollection::range(0, 10000))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
});

bench('compile', function(){
    (new CompileCollection(range(0, 10000)))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
});
