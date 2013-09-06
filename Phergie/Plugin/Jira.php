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
 * @package   Phergie_Plugin_Jira
 * @author    Phergie Development Team <team@phergie.org>
 * @copyright 2008-2011 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie_Plugin_Jira
 */

/**
 * Parses incoming messages for the references to things in Jira.
 *
 * @category Phergie
 * @package  Phergie_Plugin_Jira
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie_Plugin_Jira
 * @uses     Phergie_Plugin_Http pear.phergie.org
 */
class Phergie_Plugin_Jira extends Phergie_Plugin_Abstract
{

    /**
     * HTTP plugin
     *
     * @var Phergie_Plugin_Http
     */
    protected $http;

	/**
	 * Host of the Jira installation.
	 *
	 * @var string
	 */
	protected $host;
	
	protected $username;
	protected $password;

    /**
     * Register with the URL plugin, if possible
     *
     * @return void
     */
    public function onConnect()
    {
        $plugins = $this->getPluginHandler();
        if ($plugins->hasPlugin('Url')) {
            $plugins->getPlugin('Url')->registerRenderer($this);
        }
    }
	
    /**
     * Checks for dependencies.
     *
     * @return void
     */
    public function onLoad()
    {
        $this->http = $this->getPluginHandler()->getPlugin('Http');

		$this->host = $this->getConfig('Jira.host');
		$this->username = $this->getConfig('Jira.username');
		$this->password = $this->getConfig('Jira.password');
    }

    /**
     * Renders JIRA URLs.
     *
     * @param array $parsed parse_url() output for the URL to render
     *
     * @return bool
     */
    public function renderUrl(array $parsed)
    {
		if ($parsed['host'] != $this->host) {
			// Unable to render non-Jira URLs
			return false;
		}
		
		$source = $this->getEvent()->getSource();
		$path = $parsed['path'];
		
		if (preg_match('#/browse/([A-Z]+-[0-9]+)#', $path, $matches)) {
			$issue = $this->getIssue($matches[1]);
			
			if ($issue) {
				$this->doPrivmsg($source, $this->formatIssue($issue, false));
			}
			return true;
			
		}
		
        // if we get this far, we haven't satisfied the URL, so bail:
        return false;
    }
	
	/**
	 * Retrieves the issue from JIRA.
	 * 
	 * @param string $issue The issue key to retrieve.
	 * 
	 * @return string|boolean
	 */
	public function getIssue($issue)
	{
		$url = 'https://' . $this->username . ':' 
			 . $this->password . '@' . $this->host 
			 . '/rest/api/2/issue/' . urlencode($issue);
		$content = $this->http->get($url)->getContent();
		
		if (!empty($content->key)) {
		
			$issue = array(
				'key' => $content->key,
				'summary' => $content->fields->summary,
				'status' => $content->fields->status->name,
				'url' => 'https://' . $this->host . '/browse/' . urlencode($content->key),
			);
			
			return $issue;
			
		}
		
		// Issue not found
		return false;
	}
	
	/**
     * Formats a JIRA issue into a message suitable for output.
     *
     * @param array $issue      Array holding issue meta-data
     * @param bool  $includeUrl whether or not to include the URL in the
     *  formatted output
     *
     * @return string
     */
    protected function formatIssue($issue, $includeUrl = true)
    {
        $out = $issue['key'] . " - " . $issue['summary'] . "\n" 
			 . "Status: " . $issue['status'];
        if ($includeUrl) {
            $out .= "\n" . $issue['url'];
        }

        $encode = $this->getPluginHandler()->getPlugin('Encoding');

        return $encode->decodeEntities($out);
    }

    /**
     * Parses incoming messages for mentions of Jira related keywords.
     *
     * @return void
     */
    public function onPrivmsg()
    {
        $event = $this->getEvent();
        $source = $event->getSource();
        $message = $event->getText();
			
		// Pattern to find references to tickets using a #{number} notation
		$pattern = '/(^' . preg_quote($this->getConfig('command.prefix')) .
			'\s*)?[^\/]*?([A-Z]+-[0-9]+)/';

		// Handle all mentions of tickets.
		if (preg_match_all($pattern, $message, $matches)) {
			foreach ($matches[2] as $match) {
				$issue = $this->getIssue($match);

				if ($issue) {
					$this->doPrivmsg($source, $this->formatIssue($issue, true));
				}
			}

		}
    }
		
}
