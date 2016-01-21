<?php

namespace Test\Collection;
    
use Test\Collection\EntityCollection;

class EntityCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityCollection
     */
    private $object;
    
    public function setUp()
    {
        $this->object = new EntityCollection;
    }
    
    /**
     * @dataProvider toStringProvider
     */
    public function testToString($in, $out)
    {
        $this->object->append($in);
        $this->assertEquals($out, (string)$this->object);
    }
    
    public function toStringProvider()
    {
        return [
            [['a' => 1], '{"results":[{"a":1}],"total":0}'],
            [null, '{"results":[null],"total":0}']
        ];
    }
}
