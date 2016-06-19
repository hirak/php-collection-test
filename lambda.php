<?php
class Lambda
{
    /**
     * forbidden. (static class)
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    static function cache($args, $code)
    {
        static $cache = array();
        if (!isset($cache[$args][$code])) {
            $cache[$args][$code] = create_function($args, $code);
        }
        return $cache[$args][$code];
    }

    static function create($args, $mixed, $return = true)
    {
        static $callable;
        if (empty($callable)) {
            $callable = version_compare(\PHP_VERSION, '5.4.0', '>');
        }

        if (is_callable($mixed)) {
            if ($callable || is_string($mixed)) return $mixed;
            // @codeCoverageIgnoreStart
            if (is_array($mixed)) {
                list($obj, $method) = $mixed;
                if (is_string($obj)) {
                    return "$obj::$method";
                } else {
                    return function() use($mixed) {
                        $args = func_get_args();
                        return call_user_func_array($mixed, $args);
                    };
                }
            }
            return $mixed;
            // @codeCoverageIgnoreEnd
        } elseif (is_string($mixed)) {
            return self::cache($args, $return ? "return $mixed;" : "$mixed;");
        }

        throw new \InvalidArgumentException('$mixed must be callable');
    }
}
