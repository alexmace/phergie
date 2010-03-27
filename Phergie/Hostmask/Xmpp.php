<?php
/**
 * Phergie 
 *
 * PHP version 5
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://phergie.org/license
 *
 * @category  Phergie 
 * @package   Phergie
 * @author    Phergie Development Team <team@phergie.org>
 * @copyright 2008-2010 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie
 */

/**
 * Extension of Phergie_Hostmask with Xmpp specific regex.
 *
 * @category Phergie 
 * @package  Phergie
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie
 */
class Phergie_Hostmask_Xmpp extends Phergie_Hostmask
{

    /**
     * Regular expression used to parse a hostmask
     *
     * @var string
     */
    protected static $regex = '/^([^@]+)@([^\/]+)\/(.*)/';

    /**
     * Parses a string containing the entire hostmask into a new instance of
     * this class.
     *
     * @param string $jid Entire jid including the username, realm and resource
     *					  components
     *
     * @return Phergie_Hostmask_Xmpp New instance populated with data parsed
	 *								 from the provided hostmask string
     * @throws Phergie_Hostmask_Exception
     */
    public static function fromString($jid)
    {
        if (preg_match(self::$regex, $jid, $match)) {
            list(, $nick, $realm, $resource) = $match;
			$username = $nick . '@' . $realm;
            return new self($nick, $username, $realm);
        }

        throw new Phergie_Hostmask_Exception(
            'Invalid hostmask specified: "' . $jid . '"',
            Phergie_Hostmask_Exception::ERR_INVALID_HOSTMASK
        );
    }

}
