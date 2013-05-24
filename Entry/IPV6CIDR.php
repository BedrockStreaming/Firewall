<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IPV6 CIDR Mask Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV6CIDR extends IPV6Mask
{
    use Traits\IPCIDR;
}