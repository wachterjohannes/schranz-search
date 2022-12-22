<?php

namespace Schranz\Search\Pucene\Analysis\TokenFilter;

use Schranz\Search\Pucene\Analysis\Token;

class StandardTokenFilter implements TokenFilterInterface
{
    public function filter(Token $token): array
    {
        return [$token];
    }
}
