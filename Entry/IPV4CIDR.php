<?php
namespace M6Web\Component\Firewall\Entry;

/**
 * IPV4 CIDR Mask Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV4CIDR extends IPV4Mask
{
    use Traits\IPCIDR;
}