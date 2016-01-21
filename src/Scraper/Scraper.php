<?php

namespace Test\Scraper;
    
use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Symfony\Component\Console\Output\OutputInterface;
use Test\Collection\EntityCollection;
    
/**
 * Scrape a URL and try to match elements using a config
 */
class Scraper
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param Client $client
     * @param Collection $collection
     */
    public function __construct(Client $client, EntityCollection $collection, array $config)
    {
        $this->client = $client;
        $this->collection = $collection;
        $this->config = $config;
    }

    /**
     * Scrape URL and collect output
     * @param string $url
     * @param OutputInterface $output
     */
    public function scrape($url, OutputInterface $output)
    {
        $this->collection->exchangeArray([]);
        try {
            $initialPage = $this->client->get($url)->send();
        } catch (BadResponseException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            throw new \RuntimeException('Unable to load initial page');
        }
        $xml = $this->getSimpleXml($initialPage->getBody());
        $items = $xml->xpath($this->config['collection']);
        foreach ($items as $item) {
            $title = $item->xpath($this->config['title']);
            $unitPrice = $item->xpath($this->config['unitPrice']);
            $itemUrl = $item->xpath($this->config['itemUrl']);
            $itemSize = 0;
            $itemDesc = null;
            if (isset($itemUrl[0]->attributes()['href'])) {
                try {
                    $itemPage = $this->client->get((string)$itemUrl[0]->attributes()['href'])->send();
                    $itemPageBody = $this->getSimpleXml($itemPage->getBody());
                    $itemSize = $itemPage->getContentLength();
                    $itemDesc = $itemPageBody->xpath($this->config['desc']);
                } catch (BadResponseException $e) {
                    $output->writeln('<error>' . $e->getMessage() . '</error>');
                }
            }
            if ($title && $unitPrice) {
                $parsedPrice = (float)\trim(\str_replace('&pound', null, $unitPrice[0]));
                $this->collection->append(['title' => \trim($title[0]), 'unit_price' => $parsedPrice, 'description' => \trim($itemDesc[0]), 'size' => (\round($itemSize / 1024)) . 'kb']);
            }
        }
        
        return $this->collection;
    }
    
    private function getSimpleXml($html)
    {
        \libxml_use_internal_errors(true);
        $dom = new \DomDocument('1.0', 'UTF-8');
        $dom->strictErrorChecking = false;
        $dom->loadHTML($html);
        \libxml_use_internal_errors(false);
        
        return \simplexml_import_dom($dom);
    }
}