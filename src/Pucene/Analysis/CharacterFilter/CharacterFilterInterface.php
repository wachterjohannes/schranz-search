<?php

namespace Schranz\Search\Pucene\Analysis\CharacterFilter;

interface CharacterFilterInterface
{
    public function filter(string $input): string;
}
