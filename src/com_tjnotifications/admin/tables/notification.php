<?php
/**
 * @package     Tjnotifications
 * @subpackage  com_tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http:/www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

/**
 * table class for notification
 *
 * @since  1.6
 */
class TjnotificationTableNotification extends Table
{
	/**
	 * Constructor
	 *
	 * @param   \JDatabaseDriver  &$db  \JDatabaseDriver object.
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
		$user = Factory::getUser();

		return $user->authorise('core.create', 'com_tjnotifications') ? true : false;
	}
}
