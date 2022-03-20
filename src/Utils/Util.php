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

    public function getIdFromIri(?string $iri): int
    {
        preg_match('!\d+!', $iri, $matches, PREG_OFFSET_CAPTURE);
        if (!empty($matches)) return $matches[0][0];
        return 0;
    }
}