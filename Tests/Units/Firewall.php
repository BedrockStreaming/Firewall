<?php
namespace M6Web\Component\Firewall\Tests\Units;

require_once __DIR__ . '/../bootstrap.php';

use mageekguy\atoum;
use M6Web\Component\Firewall\Firewall as FirewallClass;

/**
 * Test du Firewall
 */
class Firewall extends atoum\test
{
    public function testInstanciate()
    {
        $firewall = new FirewallClass();

        $this
            ->assert
            ->object($firewall)
            ->isInstanceOf('M6Web\\Component\\Firewall\\Firewall')
        ;
    }

    public function testFieldDefaultState()
    {
        $object = new FirewallClass();

        $this
            ->boolean($object->getDefaultState())
                ->isFalse()
            ->object($object->setDefaultState(true))
                ->isInstanceOf('M6Web\\Component\\Firewall\\Firewall')
            ->boolean($object->getDefaultState())
                ->isTrue()
            ->exception(function () use ($object) { $object->setDefaultState('faux boolean'); })
                ->isInstanceOf('InvalidArgumentException')
            ->boolean($object->getDefaultState())
                ->isTrue()
        ;
    }

    public function testFieldIpAddress()
    {
        $object = new FirewallClass();

        $this
            ->variable($object->getIpAddress())
                ->isNull()
            ->object($object->setIpAddress('127.0.0.1'))
                ->isInstanceOf('M6Web\\Component\\Firewall\\Firewall')
            ->string($object->getIpAddress())
                ->isEqualTo('127.0.0.1')
        ;
    }

    /**
     * @dataProvider listProvider
     */
    public function testLists($list, $ips, $expectedResults)
    {
        $firewall = new FirewallClass();
        $firewall->addList($list, 'list', true);

        foreach ($ips as $key => $ip) {
            $result = $firewall
                ->setIpAddress($ip)
                ->handle()
            ;

            $this
                ->assert
                ->boolean($result)
                ->isIdenticalTo($expectedResults[$key])
            ;
        }
    }

    protected function listProvider()
    {
        $ips = array(
            '192.168.0.0',
            '192.168.0.1',
            '192.168.0.10',
            '192.168.0.42',
            '192.168.0.255',
        );

        return array(
            array(
                array('192.168.0.0/24'),
                $ips,
                array(
                    false,
                    true,
                    true,
                    true,
                    true,
                )
            ),
            array(
                array('192.168.0.40-192.168.0.50'),
                $ips,
                array(
                    false,
                    false,
                    false,
                    true,
                    false,
                )
            ),
            array(
                array('192.168.0.0/255.255.255.0'),
                $ips,
                array(
                    false,
                    true,
                    true,
                    true,
                    true,
                )
            ),
            array(
                array('192.168.0.*'),
                $ips,
                array(
                    true,
                    true,
                    true,
                    true,
                    true,
                )
            ),
            array(
                array('192.168.0.0'),
                $ips,
                array(
                    true,
                    false,
                    false,
                    false,
                    false,
                )
            ),
        );
    }
}