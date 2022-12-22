<?php

namespace Schranz\Search\Pucene\Analysis\CharacterFilter;

class ChainCharacterFilter implements CharacterFilterInterface
{
    /**
     * @var CharacterFilterInterface[]
     */
    private array $filters;

    /**
     * @param CharacterFilterInterface[] $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function filter(string $input): string
    {
        foreach ($this->filters as $filter) {
            $input = $filter->filter($input);
        }

        return $input;
    }
}
