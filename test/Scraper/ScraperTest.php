<?php

namespace Test\Scraper;
    
use Test\Scraper\Scraper;

class ScraperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityCollection
     */
    private $object;
    
    public function setUp()
    {
        $mockClient = $this->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $mockResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $mockClient->expects($this->any())->method('get')->will($this->returnValue($mockRequest));
        $htmlString = <<<EOL
            <html>
                <body>
                    <p id="title">title</p>
                    <p id="unitPrice">unitPrice</p>
                    <p id="itemUrl">itemUrl</p>
                </body>
            </html>
EOL;
        $mockRequest->expects($this->any())->method('send')->will($this->returnValue($mockResponse));
        $mockResponse->expects($this->any())->method('getBody')->will($this->returnValue($htmlString));
        $mockCollection = $this->getMockBuilder('Test\Collection\EntityCollection')
            ->disableOriginalConstructor()
            ->getMock();
        $mockCollection->expects($this->once())->method('append')->with($this->equalTo(['title' => 'title', 'description' => '', 'unit_price' => 0.0, 'size' => '0kb']));
        $config = [
			'collection' => 'body',
            'title' => 'p[@id="title"]',
            'desc' => 'p[@id="desc"]',
            'unitPrice' => 'p[@id="unitPrice"]',
            'itemUrl' => 'p[@id="itemUrl"]',
        ];
        $this->object = new Scraper($mockClient, $mockCollection, $config);
    }
    
    public function testScrape()
    {
        $mockOutput = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $this->object->scrape('test', $mockOutput);
    }
}
