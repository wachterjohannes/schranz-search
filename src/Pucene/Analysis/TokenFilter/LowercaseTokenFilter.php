<?php

namespace Schranz\Search\Pucene\Analysis\TokenFilter;

use Schranz\Search\Pucene\Analysis\Token;

class LowercaseTokenFilter implements TokenFilterInterface
{
    public function filter(Token $token): array
    {
        return [
            new Token(
                mb_strtolower($token->term),
                $token->startOffset,
                $token->endOffset,
                $token->type,
                $token->position,
            ),
        ];
    }
}
