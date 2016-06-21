<?php
const RANGEMAX = 10000;
define('EXAMPLE_ARR', range(0, RANGEMAX));
require_once __DIR__ . '/array.php';
require_once __DIR__ . '/lazy.php';
require_once __DIR__ . '/compile.php';

echo (new Collection(EXAMPLE_ARR))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
echo PHP_EOL;

echo (new LazyCollection(new ArrayIterator(EXAMPLE_ARR)))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
echo PHP_EOL;

echo (new CompileCollection(EXAMPLE_ARR))
        ->filter('$_ % 2 === 0')
        ->map('$_ ** 2')
        ->filter('$_ > 20')
        ->sum();
echo PHP_EOL;

    $sum = 0;
    foreach (EXAMPLE_ARR as $v) {
        if ($v % 2) continue;
        $v **= 2;
        if ($v <= 20) continue;

        $sum += $v;
    }

echo $sum;
echo PHP_EOL;

echo array_sum(
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
echo PHP_EOL;
