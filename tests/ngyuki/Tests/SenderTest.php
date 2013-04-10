<?php
namespace ngyuki\Tests;

use ngyuki\DynamicDNS\Sender;

/**
 *
 * @author ng
 *
 */
class SenderTest extends \PHPUnit_Framework_TestCase
{
    function test_sender_and_resolver()
    {
        $sender = new Sender('192.2.51.49', [
            'tcp_timeout' => 12,
        ]);

        $resolver = $sender->getResolver();

        assertInstanceOf('Net_DNS_Resolver', $resolver);

        assertEquals(['192.2.51.49'], $resolver->nameservers);
        assertEquals(12, $resolver->tcp_timeout);
    }
}
