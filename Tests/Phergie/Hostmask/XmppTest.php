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
 * @package   Phergie_Tests
 * @author    Phergie Development Team <team@phergie.org>
 * @copyright 2008-2010 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie_Tests
 */

/**
 * Unit tests for Pherge_Hostmask classes
 *
 * @category Phergie
 * @package  Phergie_Tests
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie_Tests
 */
class Phergie_Hostmask_XmppTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Tests the creation of the Hostmask class from an XMPP JID.
	 */
	public function testFromString()
	{
		// Create the host mask.
		$hostmask = Phergie_Hostmask_Xmpp::fromString(
			'nick@realm.server.com/resource');

		// Test that the host mask contains the expected information
		$this->assertAttributeSame('realm.server.com', 'host', $hostmask);
		$this->assertAttributeSame('nick@realm.server.com', 'nick', $hostmask);
		$this->assertAttributeSame('nick', 'username', $hostmask);

	}

	/**
	 * Tests the creation of the Hostmask class from an XMPP JID.
	 */
	public function testFromStringInvalidJid()
	{

		// Set the exception that we expect to be thrown
		$this->setExpectedException('Phergie_Hostmask_Exception');

		// Create the host mask.
		$hostmask = Phergie_Hostmask_Xmpp::fromString('this is invalid');

	}

}