<?php

namespace M6Web\Component\Firewall\Tests\Units\Entry;

require_once __DIR__ . '/../../bootstrap.php';

use M6Web\Component\Firewall\Entry\EntryInterface;

/**
 * Class EntryFactory
 *
 * @package M6\Component\Firewall\Tests\Units
 * @author  Adrien Samson <asamson.externe@m6.fr>
 */
class EntryFactory extends \mageekguy\atoum\test
{
    public function test()
    {
        $factory = new \M6Web\Component\Firewall\Entry\EntryFactory(array(__NAMESPACE__.'\EntryMock2'));

        $this
            ->object($factory->getEntry('123'))
                ->isInstanceOf(__NAMESPACE__.'\EntryMock2')
            ->boolean($factory->getEntry('1'))
                ->isEqualTo(false)
            ->object($entryList = $factory->getEntryList(array('123', '1'), true))
                ->isInstanceOf('M6Web\Component\Firewall\Lists\EntryList')
            ->array($entryList->getMatchingEntries())
                ->hasSize(1)
                ->containsValues(array('123'))
            ->boolean($entryList->isAllowed('123'))
                ->isEqualTo(true)
        ;
    }
}

class EntryMock2 implements EntryInterface
{
    protected $e;

    public function __construct($e)
    {
        $this->e = $e;
    }

    public static function match($entry)
    {
        return strlen($entry) > 1;
    }

    public function check($entry)
    {
        return $entry == $this->e;
    }

    public function getMatchingEntries()
    {
        return array($this->e);
    }
}
