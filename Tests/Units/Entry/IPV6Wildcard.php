<?php
namespace M6Web\Component\Firewall\Tests\Units\Entry;

require_once __DIR__ . '/../../bootstrap.php';

use mageekguy\atoum;
use M6Web\Component\Firewall\Entry;

/**
 * Test du type d'entrée IPV6Wildcard
 */
class IPV6Wildcard extends atoum\test
{
    /**
     * @param string $mask           Mask
     * @param string $ip             IP
     * @param array  $expectedResult Result
     *
     * @dataProvider IPProvider
     */
    public function testGoodRange($mask, $ip, $expectedResult)
    {
        $this->assert
            ->if($entry = new Entry\IPV6Wildcard($mask))
            ->then()
                ->boolean($entry->check($ip))->isIdenticalTo($expectedResult)
        ;
    }

    /**
     * @param string  $ip             IP
     * @param boolean $expectedResult Result
     *
     * @dataProvider TemplateProvider
     */
    public function testMatch($ip, $expectedResult)
    {
        $this
            ->assert
            ->boolean(Entry\IPV6Wildcard::match($ip))
            ->isIdenticalTo($expectedResult)
        ;
    }

    /**
     * Data Provider
     *
     * @return array
     */
    protected function IPProvider()
    {
        return array(
            array('0:0:0:0:1:0:0:*', '0:0:0:0:1:0:0:5', true),
            array('0:0:0:0:1:0:0:*', '0:0:0:0:1:0:1:0', false),
            array('0:0:0:0:1:0:0:*', '0:0:0:0:1:0:0:1', true),
            array('0:0:0:0:1:0:0:*', '0:0:0:0:1:0:0:0', true),
            array('0:0:0:0:1:0:0:*', '0:0:0:0:0:0:0:0', false),
        );
    }

    /**
     * Data Provider
     *
     * @return array
     */
    protected function TemplateProvider()
    {
        return array(
            array('0:0:0:0:1:0:0:*', true),
            array('0:0:0:0:1:0:0:0', false),
            array('0:0:0:z:1:0:0:*', false),
            array('0:0:z:0:1:0:0:0', false),
        );
    }
}