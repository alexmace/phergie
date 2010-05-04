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

require 'Xmpp/Connection.php';

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

	/**
	 * Set whether or not a MOTD event has been faked yet.
	 * 
	 * @var boolean 
	 */
	protected $fakedMotd = false;

	/**
	 * Holds the connection to the XMPP server.
	 * 
	 * @var Xmpp_Connection 
	 */
	protected $xmpp;

    /**
     * There isn't actually an XMPP equivilent to the IRC ACTION command, but
	 * most clients with interpret a message starting "/me" in the same way,
	 * so we'll just prepend that onto the text.
     *
     * @param string $target MUC name or user nick
     * @param string $text   Text of the action to perform
     *
     * @return void
     */
    public function doAction($target, $text)
    {
    }

    /**
     * Initiates a connection with the server.
     *
     * @return void
     */
    public function doConnect()
    {
		// Listen for input indefinitely
        set_time_limit(0);

        // Get connection information
        $connection = $this->getConnection();
        $hostname = $connection->getHost();
        $port = $connection->getPort();
        $password = $connection->getPassword();
        $username = $connection->getUsername();
        $nick = $connection->getNick();
        $realname = $connection->getRealname();
        $transport = $connection->getTransport();

		// Always default to SSL unless tcp is explicitly asked for.
		if ($transport == 'tcp') {
			$ssl = false;
		} else {
			$ssl = true;
		}

		$this->xmpp = new Xmpp_Connection(
			$username, $password, $hostname, $ssl, Zend_Log::EMERG, $port,
			'Bot');

		$this->xmpp->connect();
		$this->xmpp->authenticate();
		$this->xmpp->bind();
		$this->xmpp->establishSession();
		$this->xmpp->presence();
	}

    /**
     * There does not appear to be an XMPP equivilent for this command, so it
	 * will be left unimplemented.
     *
     * @param string $nick User nick
     * @param string $finger Finger string to send for a response
     *
     * @return void
     */
    public function doFinger($nick, $finger = null)
    {
    }

    /**
     * Invites a user to an invite-only MUC.
     *
     * @param string $nick Nick of the user to invite
     * @param string $muc  Address of the multi-user chat.
     *
     * @return void
     */
    public function doInvite($nick, $muc)
    { 
    }

    /**
     * Joins a MUC.
     *
     * @param string $mucs Comma-delimited list of mucs to join
     * @param string $keys Optional comma-delimited list of muc keys. Not in
	 *                     use in this driver.
     *
     * @return void
     */
    public function doJoin($mucs, $keys = null)
	{
		// Explode the list on the comma and join all of the channels specified
		$mucs = explode(',', $mucs);
		
		foreach ($mucs as $muc) {
			$this->xmpp->join($muc, $this->getConnection()->getNick());
		}
	}

    /**
     * Kicks a user from a MUC.
     *
     * @param string $nick   Nick of the user
     * @param string $muc    MUC address
     * @param string $reason Reason for the kick (optional)
     *
     * @return void
     */
    public function doKick($nick, $muc, $reason = null)
    {
    }

    /**
     * Obtains a list of MUCs available.
     *
     * @param string $mucs Comma-delimited list of one or more mucs to which
	 *                     the response should be restricted (optional)
     *
     * @return void
     */
    public function doList($mucs = null)
    {
    }

    /**
     * Retrieves or changes a MUC or user mode.
     *
     * @param string $target MUC name or user nick
     * @param string $mode   New mode to assign (optional)
     *
     * @return void
     */
    public function doMode($target, $mode = null)
    {
    }

    /**
     * Obtains a list of nicks of usrs in currently joined MUCs.
     *
     * @param string $mucs Comma-delimited list of one or more mucs
     *
     * @return void
     */
    public function doNames($mucs)
    {
    }

    /**
     * Changes the client nick.
     *
     * @param string $nick New nick to assign
     *
     * @return void
     */
    public function doNick($nick)
    {
	}

    /**
     * Sends a notice to a nick or MUC.
     *
     * @param string $target MUC name or user nick
     * @param string $text   Text of the notice to send
     *
     * @return void
     */
    public function doNotice($target, $text)
    {
    }

    /**
     * Leaves a MUC.
     *
     * @param string $mucs Comma-delimited list of MUCs to leave
     *
     * @return void
     */
    public function doPart($mucs)
    {
    }

    /**
     * Sends a CTCP PING request or response (they are identical) to a user.
     *
     * @param string $nick User nick
     * @param string $hash Hash to use in the handshake
     *
     * @return void
     */
    public function doPing($nick, $hash)
    {
    }

    /**
     * Responds to a server test of client responsiveness.
     *
     * @param string $daemon Daemon from which the original request originates
     *
     * @return void
     */
    public function doPong($daemon)
    {
    }

    /**
     * Sends a message to a nick or MUC.
     *
     * @param string $target MUC name or user nick
     * @param string $text   Text of the message to send
     *
     * @return void
     */
    public function doPrivmsg($target, $text)
    {
		$this->xmpp->message($target, $text);
    }

    /**
     * Terminates the connection with the server.
     *
     * @param string $reason Reason for connection termination (optional)
     *
     * @return void
     */
    public function doQuit($reason = null)
    {
	}

    /**
     * Sends a raw command to the server.
     *
     * @param string $command Command string to send
     *
     * @return void
     */
    public function doRaw($command)
    {
    }

    /**
     * Sends a CTCP TIME request to a user.
     *
     * @param string $nick User nick
     * @param string $time Time string to send for a response
     *
     * @return void
     */
    public function doTime($nick, $time = null)
    {
    }

    /**
     * Retrieves or changes a muc topic.
     *
     * @param string $muc Name of the muc
     * @param string $topic   New topic to assign (optional)
     *
     * @return void
     */
    public function doTopic($muc, $topic = null)
    {
    }

    /**
     * Sends a CTCP VERSION request or response to a user.
     *
     * @param string $nick User nick
     * @param string $version Version string to send for a response
     *
     * @return void
     */
    public function doVersion($nick, $version = null)
    {
    }

    /**
     * Retrieves information about a nick.
     *
     * @param string $nick Nick
     *
     * @return void
     */
    public function doWhois($nick)
    {
    }

    /**
     * Listens for an event on the current connection.
     *
     * @return Phergie_Event_Interface|null Event instance if an event was
     *         received, NULL otherwise
     */
    public function getEvent()
    {

		// If a MOTD has not yet been faked, do it now
		if ($this->fakedMotd == false) {

			// XMPP does not require an MOTD the same way that IRC does, so we need
			// to fake a no motd error to trigger any plugins that depend on the
			// MOTD.
			$event = new Phergie_Event_Response();
			$event->setCode(Phergie_Event_Response::ERR_NOMOTD)->setDescription('');
			$this->fakedMotd = true;

		} else {

			$tag = $this->xmpp->wait();

			// If there is no tag that means we received nothing from the server
			// and no event has occured.
			if (empty($tag)) {
				return null;
			}

			// Format the arguments as required for the command that was
			// received
			switch ($tag) {
				case 'message':
					$message = $this->xmpp->getMessage();
					$from = $message->getFrom();
					//$this->parseHostmask($from, $nick, $user, $host);
					$cmd = 'privmsg';
					$bodies = $message->getBodies();
					/**
					 * @todo There may be none or more than one body. Should
					 *		 handle that situation.
					 */
					$args = array($from, $bodies[0]['content']);

					// Prepend args with source of message so the plugins know
					// who to send the response to.
					// array_unshift($args, $from);
					break;

				case 'presence':
					unset($cmd);
					break;

				default:
					break;
			}

			if (!isset($cmd)) {
				return null;
			}

			$hostmask = Phergie_Hostmask_Xmpp::fromString($from);

			$event = new Phergie_Event_Request;
			$event->setType($cmd)
				  ->setArguments($args)
				  ->setHostmask($hostmask);

		}
        return $event;

	}

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
				 */ /*
                } elseif (!empty($cmd) && method_exists($plugin, $method)/* &&
                          !preg_match($ignore, $event->getHostmask())*//*) {
                    $plugin->{$method}();
                }
            }
		}
	}

	/**
     * Sends a /me action to a nick or muc.
     *
     * @param string $target muc name or user nick
     * @param string $text Text of the action to perform
     */
/*	public function doAction($target, $text)
	{
		$this->xmpp->message($target, '/me ' . $text);
	}
	
	public function doPrivmsg($target, $text) 
	{
		$this->xmpp->message($target, $text);
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