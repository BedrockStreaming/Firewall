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
                return strval(ip2long($long));
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
        $bits = 15; // 16 x 8 bit = 128bit
        $binParts = array();

        while ($bits >= 0) {
            $binParts[] = sprintf("%08b", (ord($ipN[$bits])));
            $bits--;
        }
        $binParts= array_reverse($binParts);

        $ipv6Long = implode("", $binParts);

        return gmp_strval(gmp_init($ipv6Long, 2), 10);
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
        $bin = gmp_strval(gmp_init($ipv6long, 10), 2);
        $bin = str_pad($bin, 128, "0", STR_PAD_LEFT);
        $ipv6Arr = array();

        $bits = 0;

        while ($bits <= 7) {
            $binPart = substr($bin, ($bits*16), 16);
            $hexPart = dechex(bindec($binPart));
            $hexFullPart = str_pad($hexPart, 4, "0", STR_PAD_LEFT);
            $ipv6[] = $hexFullPart;
            $bits++;
        }

        $ipv6 = implode(':', $ipv6);

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
        $gmpLong1  = gmp_init($long1);
        $gmpLong2  = gmp_init($long2);
        $gmpDiff   = gmp_cmp($gmpLong1, $gmpLong2);

        foreach ($operators as $operator) {
            switch(true)
            {
                case ( ( $operator === '=' ) && ( $gmpDiff == 0 ) ):
                case ( ( $operator === '<' ) && ( $gmpDiff < 0 ) ):
                case ( ( $operator === '>' ) && ( $gmpDiff > 0 ) ):
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
        return gmp_strval(gmp_and(gmp_init($long1), gmp_init($long2)));
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
        return gmp_strval(gmp_or(gmp_init($long1), gmp_init($long2)));
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
        return gmp_strval(gmp_add(gmp_init($long1), gmp_init($long2)));
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
        return gmp_strval(gmp_com(gmp_init($long)));
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