<?php

namespace Schranz\Search\Pucene\Analysis\CharacterFilter;

class StandardCharacterFilter implements CharacterFilterInterface
{
    public function filter(string $input): string
    {
        return $input;
    }
}
