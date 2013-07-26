<?php
namespace M6Web\Component\Firewall;

use M6Web\Component\Firewall\Entry\EntryFactory;
use M6Web\Component\Firewall\Lists\ListMerger;

/**
 * Firewall class
 *
 * @author Jérémy Jourdin <jjourdin.externe@m6.fr>
 */
class Firewall
{
    /**
     * @var EntryFactory Entry Factory
     */
    protected $entryFactory;

    /**
     * @var ListMerger $listMerger Lists wrapper
     */
    protected $listMerger;

    /**
     * @var boolean $defaultState Default returned value
     */
    protected $defaultState = false;

    /**
     * @varstring $ipAddress IP Address to test
     */
    protected $ipAddress;

    /**
     * Constructor
     *
     * @param EntryFactory|null $entryFactory Entry Factory
     */
    public function __construct(EntryFactory $entryFactory = null, ListMerger $listMerger = null)
    {
        if (is_null($entryFactory)) {
            $this->entryFactory = new EntryFactory();
        } else {
            $this->entryFactory = $entryFactory;
        }

        if (is_null($listMerger)) {
            $this->listMerger = new ListMerger();
        } else {
            $this->listMerger = $listMerger;
        }
    }

    /**
     * Add a list
     *
     * @param array        $list     List
     * @param string       $listName Identifier for the list
     * @param boolean|null $state    Whether the list is trusted or not
     *
     * @return $this
     */
    public function addList(array $list, $listName, $state)
    {
        if (!is_bool($state)) {
            throw new \InvalidArgumentException("Wrong parameter 'state' is not boolean");
        }

        $entryList = $this->entryFactory->getEntryList($list, $state);
        $this->listMerger->addList($entryList, $listName);

        return $this;
    }

    /**
     * Get default returned value
     *
     * @return boolean
     */
    public function getDefaultState()
    {
        return $this->defaultState;
    }

    /**
     * Set default returned value
     *
     * @param boolean $state Default returned value
     *
     * @return $this
     */
    public function setDefaultState($state)
    {
        if (!is_bool($state)) {
            throw new \InvalidArgumentException("Wrong parameter 'state' is not boolean");
        }

        $this->defaultState = $state;

        return $this;
    }

    /**
     * Get Client IP
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set Client IP
     *
     * @return $this
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Handle the current request
     *
     * @param callable $callBack Result handler
     *
     * @return boolean
     */
    public function handle(callable $callBack = null)
    {
        $ip = $this->getIpAddress();

        $isAllowed = $this->listMerger->isAllowed($ip, $this->defaultState);

        if ($callBack !== null) {
            return call_user_func($callBack, array($this, $isAllowed));
        } else {
            return $isAllowed;
        }
    }
}
