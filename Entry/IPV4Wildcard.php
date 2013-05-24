<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IPV4 Wildcard Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV4Wildcard extends IPV4Range
{
    /**
     * {@inheritdoc}
     */
    protected static $digitRegex = '((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)|\*)';

    /**
     * {@inheritdoc}
     */
    public function check($entry)
    {
        return (IPV4Range::check($entry) && AbstractIP::check($entry));
    }

    /**
     * {@inheritdoc}
     */
    protected function getCheckRegex()
    {
        $regex = $this->template;

        $patterns = array(
            '.',
            '*',
        );
        $replaces = array(
            '\.',
            parent::$digitRegex,
        );

        return sprintf('/^%s$/', str_replace($patterns, $replaces, $regex));
    }

    /**
     * {@inheritdoc}
     */
    public static function match($entry)
    {
        if (!strpos($entry, '*')) {
            return false;
        }
        
        $entry = str_replace('*', 12, $entry);

        return IPV4::match(trim($entry));
    }

    /**
     * {@inheritdoc}
     */
    public function getParts()
    {
        return array(
            'ip_start'  => str_replace('*', '0', $this->template),
            'ip_end'    => str_replace('*', '255', $this->template)
        );
    }
}