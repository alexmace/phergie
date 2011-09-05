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
 * @package   Phergie_Plugin_TerryChay
 * @author    Phergie Development Team <team@phergie.org>
 * @copyright 2008-2011 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie_Plugin_TerryChay
 */

/**
 * Parses incoming messages for the references to things in Redmine.
 *
 * @category Phergie
 * @package  Phergie_Plugin_Redmine
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie_Plugin_TerryChay
 * @uses     Phergie_Plugin_Http pear.phergie.org
 */
class Phergie_Plugin_Redmine extends Phergie_Plugin_Abstract
{

    /**
     * HTTP plugin
     *
     * @var Phergie_Plugin_Http
     */
    protected $http;

	/**
	 * URL of the Redmine installation.
	 *
	 * @var string
	 */
	protected $url;

    /**
     * Checks for dependencies.
     *
     * @return void
     */
    public function onLoad()
    {
        $this->getPluginHandler()->getPlugin('Http');

		// Get the URL and strip a slash off the end and then re-append it so
		// we are 100% sure it is there.
		$this->url = rtrim($this->getConfig('Redmine.url'), '/') . '/';
    }

	/**
	 * Fetches the details of a ticket from Redmine.
	 *
	 * @param int $ticketNumber
	 *
	 * @return string
	 */
	public function getTicket($ticketNumber)
	{

		// Construct the URL to get the ticket details.
		$url = $this->url . '/issues/' . urlencode($ticketNumber) . '.json'
			 . '?key=' . urlencode($this->getConfig('Redmine.key'));

		// Request the content.
		$content = $this->getPluginHandler()
			->getPlugin('Http')
			->get($url)
			->getContent();

		// If a ticket is found, return the details, otherwise, return false.
		if (!empty($content->subject)) {
			return '#' . $content->id . ': ' . $content->subject .
				' (URL: ' . $this->url . '/issues/' . $ticketNumber . ')';
		} else {
			return false;
		}
		
	}

    /**
     * Parses incoming messages for mentions of Redmine related keywords.
     *
     * @return void
     */
    public function onPrivmsg()
    {
        $event = $this->getEvent();
        $source = $event->getSource();
        $message = $event->getText();

		// Pattern to find references to tickets using a #{number} notation
		$pattern = '/^(' . preg_quote($this->getConfig('command.prefix')) .
            '\s*)?.*\#([0-9]+)/';

		if (preg_match($pattern, $message, $matches)) {
			if ($ticketDetails = $this->getTicket((int)$matches[2])) {
				$this->doPrivmsg($source, $ticketDetails);
			}
		}
    }
}
