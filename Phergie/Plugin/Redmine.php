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

	protected $url;

    /**
     * Checks for dependencies.
     *
     * @return void
     */
    public function onLoad()
    {
        $this->getPluginHandler()->getPlugin('Http');
		$this->url = rtrim($this->getConfig('Redmine.url'), '/');
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

		$url = $this->url . '/issues/' . urlencode($ticketNumber) . '.json'
			 . '?key=' . $this->getConfig('Redmine.key');

		$content = $this->getPluginHandler()
			->getPlugin('Http')
			->get($url)
			->getContent();

		return $content->subject .
			' (' . $this->url . '/issues/' . $ticketNumber . ')';
		
	}

    /**
     * Parses incoming messages for "Terry Chay" and related variations and
     * responds with a chayism.
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
			if ($ticketDetails = $this->getTicket($matches[2])) {
				$this->doPrivmsg($source, $ticketDetails);
			}
		}
    }
}
