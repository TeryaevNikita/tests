<?php

namespace App\Services;

class CalculateShares
{
    public function calculate(float $value): array
    {

        if ($value < 0) return null;

        $p = $value * 0.2;

        $s = ($value - $p) * 100.0 / 102.0;

        $m = $value - $p - $s;

        return [$s, $p, $m];
    }
}
