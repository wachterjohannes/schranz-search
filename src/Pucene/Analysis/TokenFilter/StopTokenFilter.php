<?php

namespace Schranz\Search\Pucene\Analysis\TokenFilter;

use Schranz\Search\Pucene\Analysis\Token;

/**
 * TODO: stopwords_path, ignore_case, remove_trailing.
 */
class StopTokenFilter implements TokenFilterInterface
{
    /**
     * @var string[]
     */
    private array $stopWords;

    /**
     * @param string[] $stopWords
     */
    public function __construct(array $stopWords = StopWords::ENGLISH)
    {
        $this->stopWords = $stopWords;
    }

    public function filter(Token $token): array
    {
        if (in_array($token->getTerm(), $this->stopWords)) {
            return [];
        }

        return [$token];
    }
}
