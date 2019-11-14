<?php

/**
 * @package    Com_Tjnotification
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * table class for notification
 *
 * @since  1.6
 */
class TjnotificationTableNotification extends \Joomla\CMS\Table\Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		$this->setColumnAlias('published', 'state');
		parent::__construct('#__tj_notification_templates', 'id', $db);
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean
	 *
	 * @see     JTable::check
	 *
	 * @since   1.0.6
	 */
	public function check()
	{
		$user   = Factory::getUser();
		$return = $user->authorise('core.create', 'com_tjnotifications') ? true : false;

		return $return;
	}
}
