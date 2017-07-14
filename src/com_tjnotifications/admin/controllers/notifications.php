<?php

/**
 * @package    Com_Tjnotification
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Notifications list controller class.
 *
 * @since  0.0.1
 */
class TjnotificationsControllerNotifications extends JControllerAdmin
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
		$input = JFactory::getApplication()->input;
		$mainframe = JFactory::getApplication();
		$extension = $input->get('extension', '', 'STRING');
		$cid = $input->get('cid', array(), 'array');
		$msg['success'] = array();
		$msg['error'] = array();
		$count = 0;
		$modelDelete = JModelList::getInstance('Notification', 'TjnotificationsModel', array('ignore_request' => true));
		$result = $modelDelete->delete($cid);

		foreach ($result as $res)
		{
			if ($res == '0')
			{
				$msg['error'] = JText::_('COM_TJNOTIFICATIONS_CORE_TEMPLATE_DELETE_MESSAGE');
			}

			if ($res == '1')
			{
				$count ++;
				$msg ['success'] = JText::sprintf('COM_TJNOTIFICATIONS_N_ITEMS_DELETED', $count);
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
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notifications&extension=' . $extension, false
			);
		}
		else
		{
			$link = JRoute::_(
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
		$value = 'user_control';
		$this->enableState($value);
	}

	/**
	 * Method to enable email status
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function enableEmailStatus()
	{
		$value = 'email_status';
		$input = JFactory::getApplication()->input;
		$post  = $input->post;
		$extension = $input->get('extension', '', 'STRING');
		$mainframe = JFactory::getApplication();

		// Get some variables from the request
		$notificationIds = $input->get('cid', array(), 'post', 'array');
		$model = JModelAdmin::getInstance('Notification', 'TJNotificationsModel');
		$success = 0;

		foreach ($notificationIds as $notificationId)
		{
			$data = $model->getItem($notificationId);

			if ($data->email_body and $data->email_subject)
			{
				$this->enableState($value);
			}
			else
			{
				$msg = JText::_('COM_TJNOTIFICATIONS_STATE_DISABLE_FAIL_MSG');
				$mainframe->enqueueMessage($msg, 'error');

				if ($extension)
				{
					$link = JRoute::_(
					'index.php?option=com_tjnotifications&view=notifications&extension=' . $extension, false
					);
				}
				else
				{
					$link = JRoute::_(
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
		$value = 'email_status';

		$this->disableState($value);
	}

	/**
	 * Method to enable state
	 *
	 * @param   string  $value  value
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function enableState($value)
	{
		$input = JFactory::getApplication()->input;
		$post  = $input->post;

		// Get some variables from the request
		$notificationIds = $input->get('cid', array(), 'post', 'array');
		$extension = $input->get('extension', '', 'STRING');
		$mainframe = JFactory::getApplication();
		$sitename = $mainframe->getCfg('sitename');
		$model = JModelAdmin::getInstance('Notification', 'TJNotificationsModel');
		$success = 0;

		foreach ($notificationIds as $notificationId)
		{
			$data = array();
			$data['id'] = $notificationId;
			$data[$value] = 1;

			if ($model->createTemplates($data))
			{
				$success = 1;
			}
		}

		if ($success)
		{
			$msg = JText::_('COM_TJNOTIFICATIONS_STATE_ENABLE_SUCCESS_MSG');
		}
		else
		{
			$msg = $model->getError();
		}

		if ($extension)
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notifications&extension=' . $extension, false
			);
		}
		else
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notifications', false
			);
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Method to undo disable user control
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function disableUserControl()
	{
		$value = "user_control";
		$this->disableState($value);
	}

	/**
	 * Method to undo disable state
	 *
	 * @param   string  $value  value
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function disableState($value)
	{
		$input = JFactory::getApplication()->input;
		$post  = $input->post;

		// Get some variables from the request
		$notificationIds = $input->get('cid', array(), 'post', 'array');
		$mainframe = JFactory::getApplication();
		$sitename = $mainframe->getCfg('sitename');
		$extension = $input->get('extension', '', 'STRING');
		$model = JModelAdmin::getInstance('Notification', 'TJNotificationsModel');
		$success = 0;

		foreach ($notificationIds as $notificationId)
		{
			$data = array();
			$data['id'] = $notificationId;
			$data[$value] = 0;

			if ($model->createTemplates($data))
			{
				$success = 1;
			}
		}

		if ($success)
		{
			$msg = JText::_('COM_TJNOTIFICATIONS_STATE_DISABLE_MSG');
		}
		else
		{
			$msg = $model->getError();
		}

		if ($extension)
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notifications&extension=' . $extension, false
			);
		}
		else
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notifications', false
			);
		}

		$this->setRedirect($link, $msg);
	}
}
