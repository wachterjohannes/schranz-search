<?php

namespace Schranz\Search\Pucene\Analysis\TokenFilter;

use Schranz\Search\Pucene\Analysis\Token;

interface TokenFilterInterface
{
    /**
     * @return Token[]
     */
    public function filter(Token $token): array;
}
