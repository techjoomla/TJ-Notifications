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
		$mainframe = JFactory::getApplication();
		$extension = $mainframe->input->get('extension', '', 'STRING');
		$cid       = $mainframe->input->get('cid', array(), 'array');
		$count     = 0;

		$msg['success'] = array();
		$msg['error']   = array();
		$modelDelete    = JModelList::getInstance('Notification', 'TjnotificationsModel');
		$result         = $modelDelete->delete($cid);

		foreach($result as $res)
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
		$mainframe       = JFactory::getApplication();
		$extension       = $mainframe->input->get('extension', '', 'STRING');
		$notificationIds = $mainframe->input->get('cid', array(), 'post', 'array');
		$model           = JModelAdmin::getInstance('Notification', 'TJNotificationsModel');
		$success         = 0;

		foreach ($notificationIds as $notificationId)
		{
			$data = $model->getItem($notificationId);

			if ($data->email_body and $data->email_subject)
			{
				$this->setState('email_status' ,1);
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
		$this->setState('email_status', 0);
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
	public function setState($value, $state)
	{
		$mainframe = JFactory::getApplication();
		$sitename  = $mainframe->getCfg('sitename');
		$model     = JModelAdmin::getInstance('Notification', 'TJNotificationsModel');
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
				$msg = JText::_('COM_TJNOTIFICATIONS_STATE_ENABLE_SUCCESS_MSG');
			}

			if ($state == 0)
			{
				$msg = JText::_('COM_TJNOTIFICATIONS_STATE_DISABLE_MSG');
			}
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
