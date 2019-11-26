<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tjnotification
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
/**
 * Methods supporting a list of tjnotification records.
 *
 * @since  1.6
 */
class TJNotificationTablePreferences extends \Joomla\CMS\Table\Table
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
