<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IPV6 Subnet Mask Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV6Mask extends IPV6Range
{
    use Traits\IPMask;

    /**
     * @static string $separatorRegex Regular expression of separator
     */
    public static $separatorRegex = '(\s*)\/(\s*)';
}