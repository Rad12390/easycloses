<?php

namespace LocalsBest\UTIL;

/**
 * @param $a
 * @param $b
 * @param $i
 * @param string $type
 * @return bool|int
 */
function indexSort($a, $b, $i, $type = 'desc')
{
  
    if ($type === 'desc') {
        if(is_numeric($a[$i])) {
            return $a[$i] > $b[$i];
        }
        return strcmp(strtolower($a[$i]), strtolower($b[$i]));
    }
    if(is_numeric($a[$i])) {
        return $a[$i] < $b[$i];
    }
    return strcmp(strtolower($b[$i]), strtolower($a[$i]));
}

/**
 * Class SortHelper
 * @package LocalsBest\UTIL
 * Helper for sorting by values
 */
class SortHelper
{
    private $index;
    private $dir;

    function __construct( $index, $dir ) {
        $this->index = $index;
        $this->dir = $dir;
    }

    function call( $a, $b ) {
        return indexSort($a, $b, $this->index, $this->dir);
    }
}
