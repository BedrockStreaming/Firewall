<?php
namespace M6Web\Component\Firewall\Entry\Traits;

/**
 * IP Subnet Mask Trait
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
trait IPMask
{
    /**
     * {@inheritdoc}
     */
    public static function getMatchRegex()
    {
        $pRegex     = IPRange::getMatchRegex();
        $regex      = substr($pRegex, 2, ( strlen($pRegex) - 4 ));
        $separator  = static::$separatorRegex;
        $maskRegex  = static::getMaskRegex();

        return  sprintf("/^%s%s%s$/", $regex, $separator, $maskRegex);
    }

    /**
     * Regular expression matching the mask
     *
     * @return string
     */
    public static function getMaskRegex()
    {
        return IPRange::getMatchRegex();
    }

    /**
     * {@inheritdoc}
     */
    public function getRange($long = true)
    {
        $parts = $this->getParts();
        $ret   = array();

        $ipLong   = $this->ip2long($parts['ip']);
        $maskLong = $this->ip2long($parts['mask']);

        $ret['begin'] = $this->IPLongAnd(
            $ipLong,
            $maskLong
        );

        $ret['end']   = $this->IPLongOr(
            $ipLong,
            $this->IPLongCom($maskLong)
        );

        if ($parts['mask'] != "255.255.255.255") {
            $ret['begin'] = $this->IPLongAdd($ret['begin'], 1);
        }

        $ret['begin'] = $this->long2ip($ret['begin']);
        $ret['end']   = $this->long2ip($ret['end']);

        if ($long) {
            $ret['begin'] = $this->ip2long($ret['begin']);
            $ret['end']   = $this->ip2long($ret['end']);
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function getParts()
    {
        $keys = array('ip', 'mask');

        return array_combine($keys, preg_split('/'. self::$separatorRegex .'/', $this->template));
    }
}