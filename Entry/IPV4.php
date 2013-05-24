<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IPV4 Firewall Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV4 extends AbstractIP
{
    /**
     * @static string $digitRegex Regular expression matching an IPV4 digit
     */
    protected static $digitRegex  = '(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)';

    /**
     * @static string $regexModel Template of the full regular expression
     */
    protected static $regexModel = '/^%1$s\.%1$s\.%1$s\.%1$s$/';

    /**
     * @static integer NB_BITS Bits amount of this entry type
     */
    const NB_BITS = 32;

    /**
     * {@inheritdoc}
     */
    public static function match($entry)
    {
        $matchRegex = sprintf(self::$regexModel, self::$digitRegex);

        return (bool) ( preg_match($matchRegex, trim($entry)) && parent::match(trim($entry)));
    }

    /**
     * {@inheritdoc}
     */
    public static function matchIp($ip)
    {
        return self::match($ip);
    }
}