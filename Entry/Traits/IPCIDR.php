<?php
namespace M6Web\Component\Firewall\Entry\Traits;

/**
 * IP CIDR Mask Trait
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
trait IPCIDR
{
    /**
     * {@inheritdoc}
     */
    public static function match($entry)
    {
        $entries = preg_split('/' . static::$separatorRegex .'/', $entry);

        if (count($entries) == 2) {
            $checkIp = static::matchIp($entries[0]);
            
            if ($checkIp && ($entries[1] >= 0) && ($entries[1] <= static::NB_BITS)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getParts()
    {
        $keys = array('ip', 'mask');
        $parts = array_combine($keys, preg_split('/'. self::$separatorRegex .'/', $this->template));

        $bin = str_pad(str_repeat('1', (int) $parts['mask']), self::NB_BITS, 0);

        $parts['mask'] = $this->long2ip($this->IPLongBaseConvert($bin, 2, 10));

        return $parts;
    }
}
