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

/*require 'XMPP/Message.php';
require 'XMPP.php';*/

/**
 * Driver that connects to an XMPP server rather than IRC, using an external
 * XMPP library.
 *
 * @category Phergie
 * @package  Phergie
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie
 */
class Phergie_Driver_Xmpp extends Phergie_Driver_Abstract
{

}
/*
	protected $startTime;

    /**
     * Flag to indicate whether or not the callbacks are currently being
     * queued for execution rather than executed outright
     *
     * @var bool
     */
/*    protected $queueing;

    /**
     * Associative array mapping command names to queued sets of arguments
     * from commands queued by callbacks
     *
     * @var array
     */
/*    protected $queue;

	protected $xmpp;

    public function run()
	{
		$this->startTime = time();
		$returnCode = $this->getIni('keepalive') ? self::RETURN_KEEPALIVE : self::RETURN_END;
        $server = $this->getIni('server');
        $port = $this->getIni('port');
        if (!$port) {
            $port = 5222;
        }
		
		$userName = $this->getIni('username');
		$password = $this->getIni('password');

		$this->xmpp = new XMPP(
			$userName, $password, $server, Zend_Log::EMERG, $port, 'Bot');

        if ($this->getIni('timeout')) {
            $timeout = $this->getIni('timeout') * 60;
        } elseif ($this->getIni('keepalive')) {
            $timeout = 600;
        } else {
            $timeout = false;
        }
        $lastPacket = time();
		
		$this->xmpp->connect();
		$this->xmpp->authenticate();
		$this->xmpp->bind();
		$this->xmpp->establishSession();
		$this->xmpp->presence();
		unset($server, $port, $userName, $password);
/*
		/**
		 * @todo Need to add some code here to become invisible on an XMPP
		 *		 server here.
		 */
/*
        // Run the onConnect handler since we successfully connected to the server
 /*       foreach($this->plugins as $plugin) {
            $plugin->onConnect();
        }*/
/*
		// Debug!
		$this->xmpp->message('admin@macefield.hollytree.co.uk', 'Testing!');
*//*
		while (true) {
			$this->queue = array();
			$this->queueing = true;

            // Clear the old event handler for every plugin
            foreach($this->plugins as $plugin) {
                $plugin->setEvent(NULL);
            }

			$tag = null;
			// At this point, need to figure out how to get what is coming in
			// from the XMPP server. Perhaps a class/array containing what it
			// is and it's contents as appropriate. Then attempt to handle in
			// some similar way to the Streams driver.
			while (empty($tag)) {
				$tag = $this->xmpp->wait();

				/*if ($stanzaTag == 'message') {
					$message = $this->xmpp->getMessage();
					//$from = $message->getFrom();
					//$bodies = $message->getBodies();
					//$buffer = $bodies[0];
				}*/
/*			}

			if (!isset($tag) || empty($tag)) {
                continue;
            }
//            $buffer = rtrim($buffer);
            $this->debug('<- ' . $tag);

			// Default the cmd to blank and the arguments to an empty array
			$cmd = '';
			$args = array();
/*
			//$this->parseHostmask($from, $nick, $user, $host);
			//list($cmd, $args) = array_pad(explode(' ', $buffer, 2), 2, null);

			//$tag = strtolower($buffer->getName());
*/
/*			// Format the arguments as required for the command that was
			// received
			switch ($tag) {
				case 'message':
					$message = $this->xmpp->getMessage();
					$from = $message->getFrom();
					$this->parseHostmask($from, $nick, $user, $host);
					$cmd = 'privmsg';
					$bodies = $message->getBodies();
					/**
					 * @todo There may be none or more than one bodies. Should
					 *		 handle that situation.
					 */
/*					$args = array($from, $bodies[0]['content']);

					// Prepend args with source of message so the plugins know
					// who to send the response to.
					// array_unshift($args, $from);
					break;

				case 'presence':
					break;

				default:
					break;
			}

            if (preg_match('/^[0-9]+$/', $cmd) > 0) {
                $event = new Phergie_Event_Response();
                $event->setCode($cmd);
                $event->setDescription($args);
/*                $event->setRawBuffer($buffer->asXML());*/
/*            } else {
                $event = new Phergie_Event_Request();
                $event->setType($cmd);
                $event->setArguments($args);
                if (isset($user)) {
                    $event->setHost($host);
                    $event->setUsername($user);
                    $event->setNick($nick);
                }
/*                $event->setRawBuffer($buffer->asXML()); */
/*            }

			/**
			 * @todo Check if the username, hostname, whatever is on the ignore
			 *		 list.
			 */
/*
			$method = 'on' . ucfirst($cmd);

			foreach($this->plugins as $plugin) {
                // Skip disabled plugins
                if (!$plugin->enabled) {
                    continue;
                }
                $plugin->setEvent($event);
                // onRaw and onTick Handlers
                $plugin->onRaw();
                $plugin->onTick();
                if ($event instanceof Phergie_Event_Response) {
                    $plugin->onResponse();
                // Skip events from ignored users and malformed packets
				/**
				 * @todo Implement a system to ignore jids
				 */
                } elseif (!empty($cmd) && method_exists($plugin, $method)/* &&
                          !preg_match($ignore, $event->getHostmask())*/) {
                    $plugin->{$method}();
                }
            }
		}
	}

	/**
     * Sends a /me action to a nick or channel.
     *
     * @param string $target Channel name or user nick
     * @param string $text Text of the action to perform
     */
/*	public function doAction($target, $text)
	{
		$this->xmpp->message($target, '/me ' . $text);
	}

	public function doInvite($nick, $channel)
	{

	}

	public function doNames($channels)
	{

	}
	
	public function doJoin($channel, $key = null) 
	{
		
	}

	public function doKick($nick, $channel, $reason = null)
	{
	}

	public function doList($channels = null)
	{

	}

	public function doMode($target, $mode = null)
	{
	}

	public function doNick($nick)
	{
	}

	public function doNotice($target, $text)
	{
	}
	
	public function doPart($channel) 
	{
		
	}

	public function doPong($daemon)
	{
	}
	
	public function doPrivmsg($target, $text) 
	{
		$this->xmpp->message($target, $text);
	}

	public function doQuit($reason = null, $reconnect = false)
	{

	}

	public function doTopic($channel, $topic = null)
	{

	}

	public function doWhois($nick)
	{
	}
	*/
    /**
     * Parses an XMPP JID in a similar manner to the way the IRC Hostmask is
	 * parsed in the abstract
     *
     * @param string $jid  JID to parse
     * @param string $nick Container for the nick
     * @param string $user Container for the username
     * @param string $host Container for the hostname
     * @return void
     */
/*    public function parseHostmask($jid, &$nick, &$user, &$host)
    {
        if (preg_match('/^([^@]+)@([^\/]+)\/.*$/', $jid, $match) > 0) {
            list(, $user, $host) = array_pad($match, 3, null);
			$nick = $user . '@' . $host;
        } else {
            $host = $hostmask;
        }
    }
}*/