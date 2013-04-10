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
class Packet
{
    /**
     * @var \Net_DNS_Packet
     */
    private $_packet;

    /**
     * @param string $zone
     */
    public function __construct($zone)
    {
        $legacy = new Legacy();

        $legacy->call(function() {
            require_once 'Net/DNS.php';
        });

        $packet = new \Net_DNS_Packet();

        $legacy->call(function() use ($packet) {
            $packet->header = new \Net_DNS_Header();
        });

        $packet->header->qr = 0;
        $packet->header->opcode = 'UPDATE';

        $packet->question = array();
        $packet->answer = array();
        $packet->authority = array();
        $packet->additional = array();

        // ZONE SECTION
        $legacy->call(function() use ($packet, $zone) {
            $packet->question[] = new \Net_DNS_Question($zone, 'SOA', 'IN');
        });

        $this->_packet = $packet;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->_packet->string();
    }

    /**
     * @param string $raw
     * @return \Net_DNS_RR
     * @throws RuntimeException
     */
    private function _rr($raw)
    {
        $legacy = new Legacy(E_STRICT | E_NOTICE);

        /* @var $rr \Net_DNS_RR */
        $rr = $legacy->call(function() use ($raw) {
            return \Net_DNS_RR::factory($raw);
        });

        if (!$rr)
        {
            throw new RuntimeException("invalid rr");
        }

        return $rr;
    }

    /**
     * @param string $raw
     */
    public function raw_prereq($raw)
    {
        $this->_packet->answer[] = $this->_rr($raw);
    }

    /**
     * @param string $raw
     */
    public function raw_update($raw)
    {
        $this->_packet->authority[] = $this->_rr($raw);
    }

    /**
     * @param string $name
     */
    public function yxdomain($name)
    {
        $this->raw_prereq("$name 0 ANY ANY");
    }

    /**
     * @param string $name
     */
    public function nxdomain($name)
    {
        $this->raw_prereq("$name 0 NONE ANY");
    }

    /**
     * @param string $req
     */
    public function update($name, $ttl, $class, $data)
    {
        $this->raw_update("$name $ttl IN $class $data");
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $data
     *
     * @throws RuntimeException
     */
    public function delete($name, $type = null, $data = null)
    {
        $class = "IN";

        if (strlen($type) === 0)
        {
            $class = "ANY";
            $type = "ANY";
            $data = null;
        }
        else if (strlen($data) === 0)
        {
            throw new RuntimeException("invalid rr");
        }

        $this->raw_update("$name 0 $class $type $data");
    }

    /**
     * @param Sender $sender
     * @return string
     */
    public function send(Sender $sender)
    {
        $legacy = new Legacy(E_STRICT);

        $resolver = $sender->getResolver();

        $packet = $this->_packet;

        $packet->header->qdcount = count($packet->question);
        $packet->header->ancount = count($packet->answer);
        $packet->header->nscount = count($packet->authority);
        $packet->header->arcount = count($packet->additional);

        $packet->header->id = $resolver->nextid();

        $data = $legacy->call(function() use ($packet) {
            return $packet->data();
        });

        $response = $resolver->send_tcp($packet, $data);

        if (!$response)
        {
            throw new RuntimeException($resolver->errorstring);
        }

        if ($response->header->rcode != 'NOERROR')
        {
            throw new RuntimeException($response->header->rcode);
        }
    }
}
