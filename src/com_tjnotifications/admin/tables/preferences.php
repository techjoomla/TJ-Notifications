<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
/**
 * Methods supporting a list of tjnotification records.
 *
 * @since  1.6
 */
class TJNotificationTablePreferences extends JTable
{
	/**
	 * Constructor.
	 *
	 * @param   array  $db  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */

	public function __construct($db)
	{
		$this->setColumnAlias('published', 'state');
		parent::__construct('#__tj_notification_user_exclusions', 'id', $db);
	}
}
