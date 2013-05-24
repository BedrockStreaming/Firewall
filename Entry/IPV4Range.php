<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IPV4 Range Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV4Range extends IPV4
{
    use Traits\IPRange;

    /**
     * @static string $separatorRegex Regular expression of separator
     */
    public static $separatorRegex = '(\s*)\-(\s*)';
}