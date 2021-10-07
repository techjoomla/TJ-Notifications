<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Class for notification logs
 *
 * @package     Tjnotifications
 * @subpackage  component
 * @since       1.0
 */
class TjnotificationsApiResourceNotificationlogs extends ApiResource
{
	/**
	 * Get method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function get()
	{
		$user = Factory::getUser();

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('l.*');
		$query->from($db->quoteName('#__tj_notification_logs', 'l'));
		$query->join('LEFT', $db->quoteName('#__tjnotifications_subscriptions', 's') .
		' ON (' . $db->quoteName('l.to') . ' = ' . $db->quoteName('s.address') . ')');
		$query->where($db->quoteName('s.user_id') . ' = ' . $db->q($user->id));
		$query->order($db->quoteName('l.date') . ' DESC');
		$query->setLimit('50');

		$db->setQuery($query);

		$logs = $db->loadObjectList();

		$this->plugin->setResponse($logs);
	}
}
