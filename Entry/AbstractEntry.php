<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * Firewall entry model
 *
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
abstract class AbstractEntry implements EntryInterface
{
    /**
     * @var string $template Entry type pattern
     */
    protected $template;

    /**
     * Constructor
     *
     * @param string $entry Entry type pattern
     */
    public function __construct($entry)
    {
        $this->template = $entry;
    }
}