<?php
namespace M6Web\Component\Firewall\Tests\Units\Entry;

require_once __DIR__ . '/../../bootstrap.php';

use mageekguy\atoum;
use M6Web\Component\Firewall\Entry;

/**
 * Test du type d'entrée IPV6
 */
class IPV6 extends atoum\test
{
    /**
     * @param string $mask           Mask
     * @param string $ip             IP
     * @param array  $expectedResult Result
     *
     * @dataProvider IPProvider
     */
    public function testGoodValue($mask, $ip, $expectedResult)
    {
        $this->assert
            ->if($entry = new Entry\IPV6($mask))
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
            array('::1','0000:0000:0000:0000:0000:0000:0000:0001',  true),
            array('::2', '0000:0000:0000:0000:0000:0000:0000:0001', false),
            array('::1', '0000:0000:0000:0000:0000:0000:0000:0010', false),
            array('::1', '0000:0000:0000:0000:0000:hijk:0000:0010', false),
        );
    }
}