<?php

namespace Schranz\Search\Pucene\Analysis\TokenFilter;

use Schranz\Search\Pucene\Analysis\Token;

class ChainTokenFilter implements TokenFilterInterface
{
    /**
     * @var TokenFilterInterface[]
     */
    private array $filters;

    /**
     * @param TokenFilterInterface[] $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function filter(Token $token): array
    {
        $tokens = [$token];
        foreach ($this->filters as $filter) {
            $tokens = $this->doFilter($filter, $tokens);
        }

        return $tokens;
    }

    /**
     * @param Token[] $tokens
     *
     * @return Token[]
     */
    private function doFilter(TokenFilterInterface $filter, array $tokens): array
    {
        $result = [];
        foreach ($tokens as $token) {
            $result = array_merge($result, $filter->filter($token));
        }

        return $result;
    }
}
