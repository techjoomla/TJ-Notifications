<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
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
