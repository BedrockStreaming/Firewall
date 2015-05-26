<?php
namespace M6Web\Component\Firewall\Tests\Units\Entry;

require_once __DIR__ . '/../../bootstrap.php';

use mageekguy\atoum;
use M6Web\Component\Firewall\Entry;

/**
 * Test du type d'entrée IPV4Mask
 */
class IPV4Mask extends atoum\test
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
            ->if($entry = new Entry\IPV4Mask($mask))
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
            array('20.66.18.0/255.255.255.0', '20.66.18.50',  true),
            array('20.66.18.0/255.255.255.0', '20.66.18.1',   true),
            array('20.66.18.0/255.255.255.0', '20.66.18.255', true),
            array('20.66.18.0/255.255.255.0', '20.66.18.0',   false),
            array('20.66.18.0/255.255.255.0', '20.66.19.50',  false),
            array('20.66.18.0/255.255.255.0', '20.66.19.0',   false),
            array('20.66.18.0/255.255.255.0', '20.66.19.255', false),
            array('20.66.18.1/255.255.255.255', '20.66.18.1', true),
        );
    }
}