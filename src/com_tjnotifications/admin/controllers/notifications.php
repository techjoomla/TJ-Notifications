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

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\CMS\MVC\Model\AdminModel;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Session\Session;

/**
 * Notifications list controller class.
 *
 * @since  0.0.1
 */
class TjnotificationsControllerNotifications extends \Joomla\CMS\MVC\Controller\AdminController
{
/**
	* Proxy for getModel.
	*
	* @param   string  $name    Optional. Model name
	* @param   string  $prefix  Optional. Class prefix
	* @param   array   $config  Optional. Configuration array for model
	*
	* @return  object	The Model
	*
	* @since    1.6
	*/
	public function getModel($name = 'Notification', $prefix = 'TjnotificationsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Method to delete notification template
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function delete()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$mainframe = Factory::getApplication();
		$input     = $mainframe->input;
		$extension = $input->get('extension', '', 'STRING');
		$cid       = $input->get('cid', array(), 'array');
		$recordId  = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$count     = 0;

		$msg['success'] = array();
		$msg['error']   = array();

		$model = $this->getModel('Notifications', 'TjnotificationsModel');
		$model->deleteTemplateConfig($recordId);

		$modelDelete    = ListModel::getInstance('Notification', 'TjnotificationsModel');
		$result         = $modelDelete->delete($cid);

		foreach ($result as $res)
		{
			if ($res == 0)
			{
				$msg['error'] = Text::_('COM_TJNOTIFICATIONS_CORE_TEMPLATE_DELETE_MESSAGE');
			}

			if ($res == 1)
			{
				$count ++;
				$msg ['success'] = Text::sprintf('COM_TJNOTIFICATIONS_N_ITEMS_DELETED', $count);
			}
		}

		if ($msg['error'])
		{
			$mainframe->enqueueMessage($msg['error'], 'error');
		}

		if ($msg['success'])
		{
			$mainframe->enqueueMessage($msg['success']);
		}

		if ($extension)
		{
			$link = Route::_(
			'index.php?option=com_tjnotifications&view=notifications&extension=' . $extension, false
			);
		}
		else
		{
			$link = Route::_(
			'index.php?option=com_tjnotifications&view=notifications', false
			);
		}

		$mainframe->redirect($link);

		return;
	}

	/**
	 * Method to enable user control
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function enableUserControl()
	{
		$user  = Factory::getUser();

		if (empty($user->authorise('core.usercontrol', 'com_tjnotifications')))
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$this->setState('user_control', 1);
	}

	/**
	 * Method to undo checkin for ticket
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function disableUserControl()
	{
		$user  = Factory::getUser();

		if (empty($user->authorise('core.usercontrol', 'com_tjnotifications')))
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$this->setState('user_control', 0);
	}

	/**
	 * Method to checkin for ticket
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function enableEmailStatus()
	{
		$mainframe       = Factory::getApplication();
		$extension       = $mainframe->input->get('extension', '', 'STRING');
		$notificationIds = $mainframe->input->get('cid', array(), 'post', 'array');
		$model           = AdminModel::getInstance('Notification', 'TJNotificationsModel');
		$user            = Factory::getUser();

		if (empty($user->authorise('core.emailstatus', 'com_tjnotifications')))
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		foreach ($notificationIds as $notificationId)
		{
			$data = $model->getItem($notificationId);

			if ($data->email['body'] and $data->email['subject'])
			{
				$this->setState('state', 1);
			}
			else
			{
				$msg = Text::_('COM_TJNOTIFICATIONS_STATE_DISABLE_FAIL_MSG');
				$mainframe->enqueueMessage($msg, 'error');

				if ($extension)
				{
					$link = Route::_(
					'index.php?option=com_tjnotifications&view=notifications&extension=' . $extension, false
					);
				}
				else
				{
					$link = Route::_(
					'index.php?option=com_tjnotifications&view=notifications', false
					);
				}

				$mainframe->redirect($link);
			}
		}
	}

	/**
	 * Method to undo disable email status
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function disableEmailStatus()
	{
		$user = Factory::getUser();

		if (empty($user->authorise('core.emailstatus', 'com_tjnotifications')))
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$this->setState('state', 0);
	}

	/**
	 * Method to enable state
	 *
	 * @param   string  $value  value
	 * @param   string  $state  state
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setState($value, $state)
	{
		$mainframe = Factory::getApplication();
		$sitename  = $mainframe->getCfg('sitename');
		$model     = AdminModel::getInstance('Notification', 'TJNotificationsModel');
		$success   = 0;

		$notificationIds = $mainframe->input->get('cid', array(), 'post', 'array');
		$extension       = $mainframe->input->get('extension', '', 'STRING');

		foreach ($notificationIds as $notificationId)
		{
			$data = array();
			$data['id'] = $notificationId;
			$data[$value] = $state;

			if ($model->createTemplates($data))
			{
				$success = 1;
			}
		}

		if ($success)
		{
			if ($state == 1)
			{
				$msg = Text::_('COM_TJNOTIFICATIONS_STATE_ENABLE_SUCCESS_MSG');
			}

			if ($state == 0)
			{
				$msg = Text::_('COM_TJNOTIFICATIONS_STATE_DISABLE_MSG');
			}
		}
		else
		{
			$msg = $model->getError();
		}

		if ($extension)
		{
			$link = Route::_(
			'index.php?option=com_tjnotifications&view=notifications&extension=' . $extension, false
			);
		}
		else
		{
			$link = Route::_(
			'index.php?option=com_tjnotifications&view=notifications', false
			);
		}

		$this->setRedirect($link, $msg);
	}
}
