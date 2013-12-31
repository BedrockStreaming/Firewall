<?php
namespace M6Web\Component\Firewall\Tests\Units\Entry;

require_once __DIR__ . '/../../bootstrap.php';

use mageekguy\atoum;
use M6Web\Component\Firewall\Entry;

/**
 * Test du type d'entrée IPV6Mask
 */
class IPV6Mask extends atoum\test
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
            ->if($entry = new Entry\IPV6Mask($mask))
            ->then()
                ->boolean($entry->check($ip))->isIdenticalTo($expectedResult)
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
            array('::0/ffff:ffff:ffff:ffff:0:0:0:0', '0:0:0:0:0:0:0:0',             false),
            array('::0/ffff:ffff:ffff:ffff:0:0:0:0', '0:0:0:0:0:0:0:1',             true),
            array('::0/ffff:ffff:ffff:ffff:0:0:0:0', '0:0:0:0:ffff:ffff:ffff:ffff', true),
            array('::0/ffff:ffff:ffff:ffff:0:0:0:0', '0:0:0:100:0:0:0:0',           false),
            array('::0/ffff:ffff:ffff:ffff:0:0:0:0', '0:0:0:0:0:0:10:0',            true),
        );
    }
}