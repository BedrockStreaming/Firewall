<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IP Entries filtering ruleset
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
abstract class AbstractIP extends AbstractEntry
{
    /**
     * {@inheritdoc}
     */
    public static function match($entry)
    {
        return (bool) @inet_pton(trim($entry));
    }

    /**
     * {@inheritdoc}
     */
    public function check($entry)
    {
        return (bool) preg_match(
            $this->getCheckRegex(),
            $this->getEntryFull($entry)
        );
    }

    /**
     * Convert IPs short notation to full notation
     *
     * @param string $entry IP
     * 
     * @return string
     */
    public function getEntryFull($entry)
    {
        return  $this->long2ip($this->ip2long($entry), false);
    }

    /**
     * Get template's regular expression
     *
     * @return string
     */
    protected function getCheckRegex()
    {
        $regex = $this->template;
        
        return sprintf('/^%s$/', str_replace('.', '\.', $regex));
    }

    /**
     * Convert IP notation to long notation (IPV6)
     *
     * @param string $long IP
     *
     * @return string IP Long
     */
    protected function ip2long($long)
    {
        switch(static::NB_BITS)
        {
            case 128:
                return $this->ip2long6($long);
            default:
                return sprintf('%u', ip2long($long));
        }
    }
    
    /**
     * Convert IP notation to long notation (IPV6)
     *
     * @param string $ipv6 IPV6
     *
     * @return string IPV6 Long
     */
    protected function ip2long6($ipv6)
    {
        $ipN = inet_pton($ipv6);
        $byte = 0;
        $ipv6Long = 0;

        while ($byte < 16) {
            $ipv6Long = bcadd(bcmul($ipv6Long, 256), ord($ipN[$byte]));
            $byte++;
        }
        return $ipv6Long;
    }

    /**
     * Convert a Long to IP Notation
     *
     * @param string  $long Long
     * @param boolean $abbr Whether or not use short notation
     *
     * @return string IP
     */
    protected function long2ip($long, $abbr = true)
    {
        switch(static::NB_BITS)
        {
            case 128:
                return $this->long2ip6($long, $abbr);
            default:
                return strval(long2ip($long));
        }
    }

    /**
     * Convert a Long to IP notation (IPV6)
     *
     * @param string  $ipv6long Long
     * @param boolean $abbr     Whether or not use short notation
     *
     * @return string IPV6
     */
    protected function long2ip6($ipv6long, $abbr = true)
    {
        $ipv6Arr = array();

        for ($part = 0; $part <= 7; $part++) {
            $hexPart = dechex(bcmod($ipv6long, 65536));
            $ipv6long = bcdiv($ipv6long, 65536, 0);
            $hexFullPart = str_pad($hexPart, 4, "0", STR_PAD_LEFT);
            $ipv6Arr[] = $hexFullPart;
        }

        $ipv6 = implode(':', array_reverse($ipv6Arr));

        if ($abbr) {
            $ipv6 = inet_ntop(inet_pton($ipv6));
        }

        return $ipv6;
    }

    /**
     * Compare 2 IP Long
     *
     * @param mixed  $long1    IP Long
     * @param mixed  $long2    IP Long
     * @param string $operator Operator
     *
     * @return boolean
     */
    protected function IPLongCompare($long1, $long2, $operator = '=')
    {
        $operators = preg_split('//', $operator);
        $diff   = bccomp($long1, $long2);

        foreach ($operators as $operator) {
            switch(true)
            {
                case ( ( $operator === '=' ) && ( $diff == 0 ) ):
                case ( ( $operator === '<' ) && ( $diff < 0 ) ):
                case ( ( $operator === '>' ) && ( $diff > 0 ) ):
                    return true;
            }
        }

        return false;
    }

    /**
     * Logic AND between two IP Longs
     *
     * @param mixed $long1 IP Long
     * @param mixed $long2 IP Long
     *
     * @return string
     */
    protected function IPLongAnd($long1, $long2)
    {
        $result = 0;
        for ($bit = 0; $bit < static::NB_BITS; $bit++) {
            $div = bcpow(2, $bit);
            $and = bcmod(bcdiv($long1, $div, 0), 2) && bcmod(bcdiv($long2, $div, 0), 2);
            $result = bcadd($result, bcmul($and, $div));
        }
        return $result;
    }

    /**
     * Logic OR between two IP Longs
     *
     * @param mixed $long1 IP Long
     * @param mixed $long2 IP Long
     *
     * @return string
     */
    protected function IPLongOr($long1, $long2)
    {
        $result = 0;
        for ($bit = 0; $bit < static::NB_BITS; $bit++) {
            $div = bcpow(2, $bit);
            $and = bcmod(bcdiv($long1, $div, 0), 2) || bcmod(bcdiv($long2, $div, 0), 2);
            $result = bcadd($result, bcmul($and, $div));
        }
        return $result;
    }

    /**
     * Addition between two IP Longs
     *
     * @param mixed $long1 IP Long
     * @param mixed $long2 IP Long
     *
     * @return string
     */
    protected function IPLongAdd($long1, $long2)
    {
        return bcadd($long1, $long2);
    }

    /**
     * Complement of an IP Longs
     *
     * @param mixed $long  IP Long
     *
     * @return string Complement
     */
    protected function IPLongCom($long)
    {
        return bcsub(bcpow(2, static::NB_BITS), bcadd($long, 1));
    }

    /**
     * Convert Long base
     *
     * @param mixed   $long     IP Long
     * @param integer $fromBase Input base
     * @param integer $toBase   Output base
     *
     * @return string
     */
    protected function IPLongBaseConvert($long, $fromBase=10, $toBase=36)
    {
        $str = trim($long);
        if (intval($fromBase) != 10) {
            $len = strlen($str);
            $q = 0;
            for ($i=0; $i<$len; $i++) {
                $r = base_convert($str[$i], $fromBase, 10);
                $q = bcadd(bcmul($q, $fromBase), $r);
            }
        } else {
            $q = $str;
        }

        if (intval($toBase) != 10) {
            $s = '';
            while (bccomp($q, '0', 0) > 0) {
                $r = intval(bcmod($q, $toBase));
                $s = base_convert($r, 10, $toBase) . $s;
                $q = bcdiv($q, $toBase, 0);
            }
        } else {
            $s = $q;
        }

        return $s;
    }

    /**
     * {@InheritDoc}
     */
    public function getMatchingEntries()
    {
        return array($this->template);
    }
}
