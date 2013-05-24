<?php
namespace M6Web\Component\Firewall\Entry;

use M6Web\Component\Firewall\Lists\EntryList;

/**
 * Entries Factory
 *
 * @author Jérémy Jourdin <jjourdin.externe@m6.fr>
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class EntryFactory
{
    protected $classes;

    /**
     * Constructor
     *
     * @param array|null $classes Classes to try when creating an entry
     */
    public function __construct(array $classes = null)
    {
        if (is_null($classes)) {
            $this->classes = array(
                __NAMESPACE__ . '\IPV4',
                __NAMESPACE__ . '\IPV4CIDR',
                __NAMESPACE__ . '\IPV4Mask',
                __NAMESPACE__ . '\IPV4Range',
                __NAMESPACE__ . '\IPV4Wildcard',
                __NAMESPACE__ . '\IPV6',
                __NAMESPACE__ . '\IPV6CIDR',
                __NAMESPACE__ . '\IPV6Mask',
                __NAMESPACE__ . '\IPV6Range',
                __NAMESPACE__ . '\IPV6Wildcard',
            );
        } else {
            $this->classes = $classes;
        }
    }

    /**
     * Get an entry matching the pattern
     *
     * @param string $entry Pattern
     *
     * @return Entry|bool
     */
    public function getEntry($entry)
    {
        foreach ($this->classes as $class) {
            if ($class::match($entry)) {
                return new $class($entry);
            }
        }

        return false;
    }

    /**
     * Get an entry list
     *
     * @param array $list    List of entries
     * @param bool  $trusted Is list trusted?
     *
     * @return EntryList
     */
    public function getEntryList(array $list, $trusted)
    {
        $flatten = array();
        $this->flattenArray($list, $flatten);
        $entries = array();
        foreach ($flatten as $elm) {
            $entry = $this->getEntry($elm);
            if ($entry) {
                $entries[] = $entry;
            }
        }
        return new EntryList($entries, $trusted);
    }

    /**
     * Flatten the array
     *
     * @param array $source
     * @param array &$dest
     */
    protected function flattenArray(array $source, array &$dest)
    {
        foreach ($source as $elm) {
            if (is_array($elm)) {
                $this->flattenArray($elm, $dest);
            } else {
                $dest[] = $elm;
            }
        }
    }
}
