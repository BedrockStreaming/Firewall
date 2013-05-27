<?php
namespace M6Web\Component\Firewall\Tests\Units\Entry;

require_once __DIR__ . '/../../bootstrap.php';

use mageekguy\atoum;
use M6Web\Component\Firewall\Entry;

/**
 * Test du type d'entrée IPV4Wildcard
 */
class IPV4Wildcard extends atoum\test
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
            ->if($entry = new Entry\IPV4Wildcard($mask))
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
            ->boolean(Entry\IPV4Wildcard::match($ip))
            ->isIdenticalTo($expectedResult);
    }

    /**
     * Data Provider
     *
     * @return array
     */
    protected function IPProvider()
    {
        return array(
            array('20.66.18.*', '20.66.18.50' , true),
            array('20.66.18.*', '20.66.18.1'  , true),
            array('20.66.18.*', '20.66.18.255', true),
            array('20.66.18.*', '20.66.18.0'  , true),
            array('20.66.18.*', '20.66.19.50' , false),
            array('20.66.18.*', '20.66.19.0'  , false),
            array('20.66.18.*', '20.66.19.255', false),
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
            array('192.168.1.*', true),
            array('192.168.1',   false),
            array('192.168.1.0', false),
            array('300.168.1.*', false),
            array('300.168.1.0', false),
        );
    }
}