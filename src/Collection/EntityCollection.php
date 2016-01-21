<?php

namespace Test\Collection;
    
use Guzzle\Guzzle\Client;
use Symfony\Component\Console\Output\OutputInterface;
use Test\Collection\Collection;

/**
 * Scrape a URL and try to match elements using a config
 */
class EntityCollection extends \ArrayObject
{
    /**
     * Return a string representation
     * @return string
     */
    public function __toString()
    {
        $items = $this->getArrayCopy();
        $data['results'] = $items;
        $data['total'] = \array_sum(\array_column($items, 'unit_price'));
                
        return \json_encode($data);
    }
}