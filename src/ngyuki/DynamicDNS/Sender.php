<?php
/**
 * DynamicDNS
 *
 * @package   ngyuki\ddns
 * @copyright 2012 Toshiyuki Goto <ngyuki.ts@gmail.com>
 * @author    Toshiyuki Goto <ngyuki.ts@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      https://github.com/ngyuki/php-ddns
 */

namespace ngyuki\DynamicDNS;

/**
 * DynamicDNS
 *
 * @package   ngyuki\ddns
 * @copyright 2012 tsyk goto <ngyuki.ts@gmail.com>
 * @author    tsyk goto <ngyuki.ts@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      https://github.com/ngyuki/php-ddns
 */
class Sender
{
    /**
     * @var \Net_DNS_Resolver
     */
    private $_resolver;

    /**
     * @param string $addr
     * @param array $cfg
     */
    public function __construct($nameserver, $cfg = array())
    {
        $cfg += array();

        $legacy = new Legacy();
        $legacy->call(function() {
            require_once 'Net/DNS.php';
        });

        $this->_resolver = new \Net_DNS_Resolver($cfg);
        $this->_resolver->set_nameservers($nameserver);
    }

    /**
     * @return \Net_DNS_Resolver
     */
    public function getResolver()
    {
        return $this->_resolver;
    }

    /**
     * @param Packet $packet
     *
     * @throws RuntimeException
     */
    public function send(Packet $packet)
    {
        $packet->send($this);
    }
}
