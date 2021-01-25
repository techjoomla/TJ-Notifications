<?php
/**
 * @package     Tjnotifications
 * @subpackage  api.tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Class for checkin to tickets for mobile APP
 *
 * @package     Tjnotifications
 * @subpackage  component
 * @since       1.0
 */
class TjnotificationsApiResourceMessages extends ApiResource
{
	/**
	 * POST method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function post()
	{
		ApiError::raiseError(400, Text::_('POST method not supported'));
	}

	/**
	 * Subscribe method
	 *
	 * @return  json
	 *
	 * @since   1.0
	 */
	public function get()
	{
		$input = Factory::getApplication()->input;
		$user  = Factory::getUser();

		$start  = $input->get('start', 0, 'int');
		$limit  = $input->get('limit', '', 'int');
		$userid = $input->get('userid', '', 'int');

		if (empty($userid))
		{
			$userid = $user->id;
		}

		// Get TJNotifications notifications list
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_tjnotifications/models', 'MessagesModel');
		$model = ListModel::getInstance('Messages', 'TjnotificationsModel', array('ignore_request' => true));

		$model->setState("list.limit", $limit);
		$model->setState("list.start", $start);
		$model->setState("filter.recepient", $userid);
		$notifications = $model->getItems();

		$result = array();

		if (empty($notifications))
		{
			$result['message'] = Text::_("No data");
		}
		else
		{
			$result['notifications']          = $notifications;
			$result['notifications']['total'] = $model->getTotal();
		}

		$this->plugin->setResponse($result);
	}
}
