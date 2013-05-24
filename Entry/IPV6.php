<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IPV6 Firewall Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV6 extends AbstractIP
{
    /**
     * @static string $digitRegex Regular expression matching an IPV6 digit
     */
    protected static $digitRegex = '([0-9A-Fa-f]{1,4})';

    /**
     * @static integer NB_BITS Bits amount of this entry type
     */
    const NB_BITS = 128;

    /**
     * {@inheritdoc}
     */
    public static function match($entry)
    {
        return (bool) (!IPV4::match(trim($entry)) && AbstractIP::match(trim($entry)));
    }

    /**
     * {@inheritdoc}
     */
    public function check($entry)
    {
        if (!IPV6::matchIp($entry)) {
            return false;
        }
        
        $entryLong = $this->ip2long($entry);
        $templateLong = $this->ip2long($this->template);

        return (bool) $this->IPLongCompare($entryLong, $templateLong, '=');
    }

    /**
     * Get a digit different of those in the IP
     *
     * @param string $entry IPV6
     *
     * @return string
     */
    public static function getFreeDigit($entry)
    {
        do {
            $digit = dechex(rand(0, 65535));
        } while (strstr($entry, $digit));

        return $digit;
    }

    /**
     * {@inheritdoc}
     */
    public static function matchIp($ip)
    {
        return IPV6::match($ip);
    }
}