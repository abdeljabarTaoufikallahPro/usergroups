<?php

namespace App\Utils;

class Util
{
    public function fakeNumber($requiredLength = 9, $highestDigit = 9) {
        $sequence = '';

        for ($i = 0; $i < $requiredLength; ++$i) {
            $sequence .= mt_rand(0, $highestDigit);
        }

        return sprintf('0%s', $sequence);
    }
}