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

/**
 * Methods supporting a list of tjnotification records.
 *
 * @since  1.6
 */
class TJNotificationsTableProvider extends \Joomla\CMS\Table\Table
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
		parent::__construct('#__tj_notification_providers', 'id', $db);
	}
}
