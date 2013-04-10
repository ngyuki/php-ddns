<?php
namespace ngyuki\Tests;

use ngyuki\DynamicDNS\Legacy;

/**
 *
 * @author ng
 *
 */
class LegacyTest extends \PHPUnit_Framework_TestCase
{
    private $error_reporting;

    function setUp()
    {
        $this->error_reporting = error_reporting();
    }

    function tearDown()
    {
        error_reporting($this->error_reporting);
    }

    function test_retval()
    {
        error_reporting(-1);

        $legacy = new Legacy(E_STRICT|E_NOTICE|E_WARNING);

        $actual = $legacy->call(function(){
            assertSame(-1 &~ E_STRICT &~ E_NOTICE &~ E_WARNING, error_reporting());
            return 12345;
        });

        assertSame(12345, $actual);

        assertSame(-1, error_reporting());
    }

    function test_throw_exception()
    {
        $legacy = new Legacy();

        $this->setExpectedException(get_class(new \UnexpectedValueException), "asdsegf4era5042a");

        $legacy->call(function(){
            throw new \UnexpectedValueException("asdsegf4era5042a");
        });
    }
}
