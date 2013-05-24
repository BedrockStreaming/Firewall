<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IPV6 Range Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV6Range extends IPV6
{
    use Traits\IPRange;

    /**
     * @static string $separatorRegex Regular expression of separator
     */
    public static $separatorRegex = '(\s*)\-(\s*)';
}