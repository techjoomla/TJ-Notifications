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
class TjnotificationsApiResourceMessageMarkAsRead extends ApiResource
{
	/**
	 * POST method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function get()
	{
		ApiError::raiseError(400, Text::_('GET method not supported'));
	}

	/**
	 * Subscribe method
	 *
	 * @return  json
	 *
	 * @since   1.0
	 */
	public function post()
	{
		$input = Factory::getApplication()->input;
		$pk    = $input->post->get('id', 0, 'int');

		if (empty($pk))
		{
			ApiError::raiseError(400, Text::_('ID field not passed'));
		}

		// Get TJNotifications notifications list
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_tjnotifications/models', 'MessageformModel');
		$messageFormModel = BaseDatabaseModel::getInstance('Messageform', 'TjnotificationsModel');

		$result = array();

		try
		{
			// Mark these as read
			$messageFormModel->updateNotificationStatus('read', $pk, 1);
			$result['success'] = true;
		}
		catch (\Exception $e)
		{
			ApiError::raiseError(400, Text::_($e->getMessage()));
		}

		$this->plugin->setResponse($result);
	}
}
