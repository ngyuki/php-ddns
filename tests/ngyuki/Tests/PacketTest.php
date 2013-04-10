<?php
namespace ngyuki\Tests;

use ngyuki\DynamicDNS\Packet;
use ngyuki\DynamicDNS\RuntimeException;

/**
 *
 * @author ng
 *
 */
class PacketTest extends \PHPUnit_Framework_TestCase
{
    function test_tostring()
    {
        $packet = new Packet("hoge.invalid");
        assertNotEmpty((string)$packet);

        $packet->delete("aaa.hoge.invalid");
        assertContains("aaa.hoge.invalid", (string)$packet);
    }

    function test_delete_invalid_rr()
    {
        $packet = new Packet("hoge.invalid");

        $this->setExpectedException(get_class(new RuntimeException), "invalid rr");
        $packet->delete("aaa.hoge.invalid", "A");
    }
}
