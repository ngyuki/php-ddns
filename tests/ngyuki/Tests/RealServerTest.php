<?php
namespace ngyuki\Tests;

use ngyuki\DynamicDNS\Sender;
use ngyuki\DynamicDNS\Packet;
use ngyuki\DynamicDNS\RuntimeException;

/**
 *
 * @author ng
 *
 */
class RealServerTest extends \PHPUnit_Framework_TestCase
{
    private $sender;

    function setUp()
    {
        $server = getenv('PHPUNIT_NAMESERVER');
        $domain = getenv('PHPUNIT_TESTDOMAIN');

        if (!strlen($server) || !strlen($domain))
        {
            $this->markTestSkipped();
        }

        $this->sender = new Sender($server, [
            'tcp_timeout' => 1,
        ]);
    }

    function test_prepare()
    {
        $domain = getenv('PHPUNIT_TESTDOMAIN');

        $packet = new Packet($domain);

        $packet->delete("aaa.$domain");
        $packet->delete("bbb.$domain");
        $packet->delete("zzz.$domain");

        $packet->update("aaa.$domain", 3600, "A", "192.2.45.81");

        $this->sender->send($packet);
    }

    function test_prereq()
    {
        $domain = getenv('PHPUNIT_TESTDOMAIN');

        $packet = new Packet($domain);

        $packet->nxdomain("zzz.$domain");
        $packet->yxdomain("aaa.$domain");

        $this->sender->send($packet);
    }

    function test_yxdomain()
    {
        $domain = getenv('PHPUNIT_TESTDOMAIN');

        $packet = new Packet($domain);

        $packet->yxdomain("zzz.$domain");

        $this->setExpectedException(get_class(new RuntimeException), "NXDOMAIN");
        $this->sender->send($packet);
    }

    function test_nxdomain()
    {
        $domain = getenv('PHPUNIT_TESTDOMAIN');

        $packet = new Packet($domain);

        $packet->nxdomain("aaa.$domain");

        $this->setExpectedException(get_class(new RuntimeException), "YXDOMAIN");
        $this->sender->send($packet);
    }

    function test_multi()
    {
        $domain = getenv('PHPUNIT_TESTDOMAIN');

        $packet = new Packet($domain);

        // nxdomain
        $packet->nxdomain("zzz.$domain");

        // yxdomain
        $packet->yxdomain("aaa.$domain");

        // delete
        $packet->delete("aaa.$domain", "A", "192.2.45.81");
        $packet->delete("bbb.$domain");

        // update
        $packet->update("aaa.$domain", 3600, "A",     "192.2.45.81");
        $packet->update("bbb.$domain", 3600, "CNAME", "aaa.test");

        $this->sender->send($packet);
    }

    function test_timeout()
    {
        $sender = new Sender("192.2.3.4", [
            'tcp_timeout' => 1,
        ]);

        $packet = new Packet("invalid");
        $packet->delete("zzz.invalid");

        $this->setExpectedException(get_class(new RuntimeException), "connection failed");
        $sender->send($packet);
    }
}
