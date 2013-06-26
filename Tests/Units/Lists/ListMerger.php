<?php

namespace M6Web\Component\Firewall\Tests\Units\Lists;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Class ListMerger
 *
 * @package M6Web\Component\Firewall\Tests\Units
 * @author  Adrien Samson <asamson.externe@m6.fr>
 */
class ListMerger extends \mageekguy\atoum\test
{
    public function test()
    {
        $entryList1 = new \mock\M6Web\Component\Firewall\Lists\EntryList();
        $entryList1->getMockController()->isAllowed = function ($e) {
            if ($e == '42' || $e == 'conflict') {
                return true;
            }

            return null;
        };

        $entryList2 = new \mock\M6Web\Component\Firewall\Lists\EntryList();
        $entryList2->getMockController()->isAllowed = function ($e) {
            if ($e == '666' || $e == 'conflict') {
                return false;
            }

            return null;
        };

        $listMerger = new \M6Web\Component\Firewall\Lists\ListMerger();

        $this
            ->object($listMerger->addList($entryList1, 'list1'))
                ->isEqualTo($listMerger)
            ->object($listMerger->addList($entryList2, 'list2'))
                ->isEqualTo($listMerger)
            ->boolean($listMerger->isAllowed('42', true))
                ->isEqualTo(true)
            ->boolean($listMerger->isAllowed('42', false))
                ->isEqualTo(true)
            ->boolean($listMerger->isAllowed('666', true))
                ->isEqualTo(false)
            ->boolean($listMerger->isAllowed('666', false))
                ->isEqualTo(false)
            ->boolean($listMerger->isAllowed('123', true))
                ->isEqualTo(true)
            ->boolean($listMerger->isAllowed('123', false))
                ->isEqualTo(false)
            ->boolean($listMerger->isAllowed('conflict', true))
                ->isEqualTo(true)
            ->boolean($listMerger->isAllowed('conflict', false))
                ->isEqualTo(false)
        ;
    }
}
