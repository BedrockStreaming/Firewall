<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IPV4 Subnet Mask Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV4Mask extends IPV4Range
{
    use Traits\IPMask;

    /**
     * @static string $separatorRegex Regular expression of separator
     */
    public static $separatorRegex = '(\s*)\/(\s*)';
}