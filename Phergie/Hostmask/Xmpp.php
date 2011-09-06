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
 * @package   Phergie_Driver_Xmpp
 * @author    Phergie Development Team <team@phergie.org>
 * @copyright 2008-2010 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie_Driver_Xmpp
 */

/**
 * Extension of Phergie_Hostmask with Xmpp specific regex.
 *
 * @category Phergie 
 * @package  Phergie_Driver_Xmpp
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie_Driver_Xmpp
 */
class Phergie_Hostmask_Xmpp extends Phergie_Hostmask
{

    /**
     * Regular expression used to parse a hostmask
     *
     * @var string
     */
    protected static $regex = '/^(([^@]+)@)?([^\/]+)\/?(.*)/';

    /**
     * Parses a string containing the entire hostmask into a new instance of
     * this class.
     *
     * @param string $jid  Entire jid including the username, realm and resource
     *					   components
	 * @param string $type The type of message the jid has come from. This is 
	 *					   important because the contents of the jid have 
	 *					   different meanings in different contexts. 
     *
     * @return Phergie_Hostmask_Xmpp New instance populated with data parsed
	 *								 from the provided hostmask string
     * @throws Phergie_Hostmask_Exception
     */
    public static function fromString($jid, $type = 'chat')
    {
        if (preg_match(self::$regex, $jid, $match)) {
			if ($type == 'groupchat') {
				//var_dump($match);
				//exit;
				list(, , $room, $realm, $nick) = $match;
				$username = '';
			} else {
				list(, , $username, $realm, $resource) = $match;
				$nick = $username . '@' . $realm;
			}
			
			
            return new self($nick, $username, $realm);
        }

        throw new Phergie_Hostmask_Exception(
            'Invalid hostmask specified: "' . $jid . '"',
            Phergie_Hostmask_Exception::ERR_INVALID_HOSTMASK
        );
    }

}
