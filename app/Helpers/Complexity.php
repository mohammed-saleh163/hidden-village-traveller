<?php

namespace App\Helpers;

class Complexity
{

    public static function calculateComplexity(int $hops, int $timePerHop)
    {
        $num = pow($timePerHop, $hops);

        $base = 2;

        $complexity = log($num, $base);

        return $complexity;
    }


}
