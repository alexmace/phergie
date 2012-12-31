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
 * @package   Phergie_Plugin_Redmine
 * @author    Phergie Development Team <team@phergie.org>
 * @copyright 2008-2011 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie_Plugin_Redmine
 */

/**
 * Parses incoming messages for the references to things in Redmine.
 *
 * @category Phergie
 * @package  Phergie_Plugin_Redmine
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie_Plugin_Redmine
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
		$url = $this->url . 'issues/' . urlencode($ticketNumber) . '.json'
			 . '?key=' . urlencode($this->getConfig('Redmine.key'));

		// Request the content.
		$content = $this->getPluginHandler()
			->getPlugin('Http')
			->get($url)
			->getContent();

		// If a ticket is found, return the details, otherwise, return false.
		if (!empty($content->issue->subject)) {
			return 'Issue ' . $content->issue->id . ': ' . $content->issue->subject .
				' (URL: ' . $this->url . 'issues/' . $ticketNumber . ')';
		} else {
			return false;
		}
		
	}
	
	/**
	 * Assigns a ticket to someone in Redmine.
	 * 
	 * @param array  $issues   Issues to be assigned.
	 * @param string $assignee Users the issues are to be assigned to.
	 * @param type   $source   JID the command came from.
	 * @param type   $nick     Nickname of the person who requested it.
	 */
	public function assignTicket($issues, $assignee, $source, $nick)
	{
		
		$userMappings = $this->getConfig('Redmine.mapping');
		
		// Check if we have any user mapping available that will map their 
		// jabber nickname (or anything) to their Redmine user.
		if (isset($userMappings[strtolower($assignee)])) {
			$assignee = $userMappings[strtolower($assignee)];
		}
			
		// First thing we need to do is attempt to find out the ID of the user
		// we want to assign the ticket to to.

		// Query Redmine for the user ID
		$url = $this->url . 'users.json'
			 . '?key=' . urlencode($this->getConfig('Redmine.key'))
			 . '&name=' . urlencode($assignee);

		// Request the content.
		$content = $this->getPluginHandler()
			->getPlugin('Http')
			->get($url)
			->getContent();
		
		// This means a user was found.
		if ($content->total_count > 0) {
			
			$userId = $content->users[0]->id;
			
			foreach ($issues as $issue) {
			
				$url = $this->url . 'issues/' . urlencode($issue) . '.json'
				 . '?key=' . urlencode($this->getConfig('Redmine.key'));

				$context['method'] = 'PUT';
				$context['header'] = 'Content-Type: application/json';
				$context['content'] = '
{
	"issue": {
		"assigned_to_id": ' . $userId . ',
		"notes": Assigned to ' . $assignee . ' by ' . $nick . ' in ' . $source . ' 
	}
}';
				$response = $this->getPluginHandler()
								 ->getPlugin('Http')
								 ->request($url, $context);
			
				$this->doPrivmsg($source, 'Issue ' . $issue . ' assigned to ' . $assignee);
					
			}
			
		}
	}

    /**
     * Parses incoming messages for mentions of Redmine related keywords.
     *
     * @return void
     */
    public function onPrivmsg($command)
    {
        $event = $this->getEvent();
        $source = $event->getSource();
        $message = $event->getText();

		// Only do this if the message is not an "assign" message.
		if (strtolower($command) != 'assign') {
			
			// Pattern to find references to tickets using a #{number} notation
			$pattern = '/(^' . preg_quote($this->getConfig('command.prefix')) .
				'\s*)?.*?\#([0-9]+)/';

			// Handle all mentions of tickets.
			if (preg_match_all($pattern, $message, $matches)) {
				foreach ($matches[1] as $match) {
					if ($ticketDetails = $this->getTicket((int)$match)) {
						$this->doPrivmsg($source, $ticketDetails);
					}
				}
			
			}
		}
    }
	
	/**
	 * Handles 'assign' commands.
	 * 
	 * @return void
	 */
	public function onCommandAssign()
	{
		
		$event = $this->getEvent();
		$source = $event->getSource();
		$message = $event->getText();
		
		$args = func_get_args();
		
		$issues = array();
		$assignee = $event->getNick();
		
		foreach ($args as $arg) {
			
			// If it looks like an issue number, treat it as such.
			if (preg_match('/^\#([0-9]+),?$/', $arg, $matches)) {
				$issues[] = $matches[1];
			} else if (preg_match('/^([a-z]+),?$/i', $arg, $matches) && count($issues) > 0) {
				$this->assignTicket($issues, $matches[1], $source, $event->getNick());
				$issues = array();
			}
			
		}
		
		if (count($issues) > 0) {
			$this->assignTicket($issues, $assignee, $source, $event->getNick());
		}	
		
	}
	
}
