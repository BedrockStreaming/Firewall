<?php
namespace M6Web\Component\Firewall\Lists;

/**
 * Firewall rules wrapper
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class EntryList
{
    /**
     * @var array $entries Registered entries
     */
    protected $entries;

    /**
     * @var boolean $matchingResponse Response for a matching entity
     */
    protected $matchingResponse;

    /**
     * Constructor
     *
     * @param array   $list    Array with entries
     * @param boolean $trusted Whether or not this list is trusted by the firewall
     */
    public function __construct(array $list = array(), $trusted = false)
    {
        $this->entries = $list;
        $this->matchingResponse = $trusted;
    }

    /**
     * Whether or not the Entry is allowed by this list
     *
     * @param string $entry Entry
     *
     * @return boolean|null TRUE = allowed, FALSE = rejected, NULL = not handled
     */
    public function isAllowed($entry)
    {
        foreach ($this->entries as $elmt) {
            if ($elmt->check($entry)) {
                return $this->matchingResponse;
            }
        }

        return null;
    }

    /**
     * Resolve all entries match the list
     *
     * @return array
     */
    public function getMatchingEntries()
    {
        $matchingEntries = array();

        foreach ($this->entries as $entry) {
            $matchingEntries = array_merge($matchingEntries, $entry->getMatchingEntries());
        }

        return $matchingEntries;
    }
}