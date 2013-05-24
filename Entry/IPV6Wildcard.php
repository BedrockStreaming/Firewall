<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IPV6 Wildcard Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV6Wildcard extends IPV6Range
{
    /**
     * {@inheritdoc}
     */
    protected static $digitRegex = '(([0-9A-Fa-f]{1,4})|\*)';

    protected $freeDigit;

    /**
     * {@inheritdoc}
     */
    public function check($entry)
    {
        return (IPV6Range::check($entry) && AbstractIP::check($entry));
    }

    /**
     * {@inheritdoc}
     */
    protected function getCheckRegex()
    {
        $digit = IPV6::getFreeDigit($this->template);
        $templateFull = str_replace(
            $digit,
            '*',
            $this->long2ip(
                $this->ip2long(
                    str_replace('*', $digit, $this->template)
                ),
                false
            )
        );

        $regex = '/^'. str_replace('*', IPV6::$digitRegex, $templateFull) .'$/';

        return $regex;
    }

    /**
     * {@inheritdoc}
     */
    public static function match($entry)
    {
        if (!strpos($entry, '*')) {
            return false;
        }
        
        $rpDigit = IPV6::getFreeDigit($entry);
        $entry = str_replace('*', $rpDigit, $entry);

        return IPV6::match(trim($entry));
    }

    /**
     * {@inheritdoc}
     */
    public function getParts()
    {
        return array(
            'ip_start'  => str_replace('*', '0000', $this->template),
            'ip_end'    => str_replace('*', 'ffff', $this->template)
        );
    }
}