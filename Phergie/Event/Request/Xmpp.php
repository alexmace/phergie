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
 * @copyright 2008-2011 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie_Driver_Xmpp
 */

/**
 * Autonomous XMPP event originating from a user or the server.
 *
 * @category Phergie
 * @package  Phergie_Driver_Xmpp
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie_Driver_Xmpp
 * @link     http://www.irchelp.org/irchelp/rfc/chapter4.html
 */
class Phergie_Event_Request_Xmpp
    extends Phergie_Event_Request
{
	
	/**
     * Regular expression used to parse a hostmask to see if this is a room.
     *
	 * The specs don't actually say that conference rooms should be on 
	 * conference.hostname, so this is actually a dirty hack, but it'll do the
	 * job for now.
	 * 
     * @var string
     */
    protected static $regex = '/^([^@]+)@conference([^\/]+)\/?(.*)/';

    /**
     * Determines whether a given string is a valid XMPP room name.
     *
     * @param string $string String to analyze
     *
     * @return bool TRUE if $string contains a valid channel name, FALSE
     *         otherwise
     */
    protected function isChannelName($string)
    {
		
        return (preg_match(self::$regex, $string) > 0);
    }

}
