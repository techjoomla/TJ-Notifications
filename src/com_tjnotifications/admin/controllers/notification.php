<?php

/**
 * @package    Com_Tjnotification
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Notification controller class.
 *
 * @since  0.0.1
 */
class TjnotificationsControllerNotification extends JControllerForm
{
	/**
	 * Function to add field data
	 *
	 * @return  void
	 */
	public function editSave()
	{
		$input     = JFactory::getApplication()->input;
		$cid       = $input->post->get('cid', array(), 'array');
		$recordId  = (int) (count($cid) ? $cid[0] : $input->getInt('id'));

		if (parent::save($data))
		{
			$msg = JText::_('COM_TJNOTIFICATIONS_FIELD_CREATED_SUCCESSFULLY');
		}
		else
		{
			$msg = JText::_('COM_TJNOTIFICATIONS_MODEL_NOTIFICATION_KEY_DUPLICATE_MESSAGE');
		}

		$extension = $input->get('extension', '', 'STRING');

		if ($extension)
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit&id=' . $recordId .
			'&extension=' . $extension, false
			);
		}
		else
		{
			$link = JRoute::_('index.php?option=com_tjnotifications&view=notification&layout=edit&id=' . $recordId, false);
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Function to save field data
	 *
	 * @param   string  $key     key
	 * @param   string  $urlVar  urlVar
	 *
	 * @return  void
	 */
	public function saveClose($key = null, $urlVar = null)
	{
		$input     = JFactory::getApplication()->input;
		$extension = $input->get('extension', '', 'STRING');

		if (parent::save($data))
		{
			$msg = JText::_('COM_TJNOTIFICATIONS_FIELD_CREATED_SUCCESSFULLY');
		}
		else
		{
			$msg = JText::_('COM_TJNOTIFICATIONS_MODEL_NOTIFICATION_KEY_DUPLICATE_MESSAGE');
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
	 * Function to edit field data
	 *
	 * @param   string  $key     key
	 * @param   string  $urlVar  urlVar
	 *
	 * @return  void
	 */
	public function edit($key = null, $urlVar = null)
	{
		$input     = JFactory::getApplication()->input;
		$cid       = $input->get('cid', array(), 'post', 'array');
		$recordId  = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$extension = $input->get('extension', '', 'STRING');

		if ($extension)
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit&id=' . $recordId .
			'&extension=' . $extension, false
			);
		}
		else
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit&id=' . $recordId, false
			);
		}

		$this->setRedirect($link);
	}

	/**
	 * Function to cancel the operation on field
	 *
	 * @param   string  $key  key
	 *
	 * @return  void
	 */
	public function cancel($key = null)
	{
		$input     = JFactory::getApplication()->input;
		$extension = $input->get('extension', '', 'STRING');

		if ($extension)
		{
			$link = JRoute::_('index.php?option=com_tjnotifications&view=notifications&extension=' . $extension, false
			);
		}
		else
		{
			$link = JRoute::_('index.php?option=com_tjnotifications&view=notifications', false
		);
		}

		$this->setRedirect($link);
	}

	/**
	 * Function to save field data
	 *
	 * @param   string  $key     key
	 * @param   string  $urlVar  urlVar
	 *
	 * @return  void
	 */
	public function saveNew($key = null, $urlVar = null)
	{
		$input    = JFactory::getApplication()->input;
		$cid      = $input->post->get('cid', array(), 'array');
		$recordId = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$extension = $input->get('extension', '', 'STRING');

		if (parent::save($data))
		{
			$msg = JText::_('COM_TJNOTIFICATIONS_FIELD_CREATED_SUCCESSFULLY');
		}
		else
		{
			$msg = JText::_('COM_TJNOTIFICATIONS_MODEL_NOTIFICATION_KEY_DUPLICATE_MESSAGE');
		}

		if ($extension)
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit&extension=' . $extension, false
			);
		}
		else
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit', false
			);
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Function to save field data
	 *
	 * @param   string  $key     key
	 * @param   string  $urlVar  urlVar
	 *
	 * @return  void
	 */
	public function add($key = null, $urlVar = null)
	{
		$input     = JFactory::getApplication()->input;
		$cid       = $input->post->get('cid', array(), 'array');
		$recordId  = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$extension = $input->get('extension', '', 'STRING');

		if ($extension)
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit&extension=' . $extension, false
			);
		}
		else
		{
			$link = JRoute::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit', false
			);
		}

		$this->setRedirect($link);
	}

	/**
	 * Function to print sample data for email template
	 *
	 * @return  void
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function getSampleData()
	{
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/models');
		$notificationsModel = BaseDatabaseModel::getInstance('Notification', 'TJNotificationsModel');

		$input = Factory::getApplication()->input;
		$id = $input->get('id');

		$data = $notificationsModel->getSampleBodyData($id);

		echo $data;
		jexit();
	}
}
