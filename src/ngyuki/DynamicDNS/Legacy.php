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
class Legacy
{
    private $_ignore;

    /**
     * @param int $ignore
     */
    public function __construct($ignore = E_STRICT)
    {
        $this->_ignore = $ignore;
    }

    /**
     * @param callable $func
     * @return mixed
     * @throws \Exception
     */
    public function call(callable $func)
    {
        $args = func_get_args();
        array_shift($args);

        $er = error_reporting();
        error_reporting($er &~ $this->_ignore);

        try
        {
            $ret = call_user_func_array($func, $args);
            error_reporting($er);
            return $ret;
        }
        catch (\Exception $ex)
        {
            error_reporting($er);
            throw $ex;
        }
    }
}
