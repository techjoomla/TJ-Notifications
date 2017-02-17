<?php

/**
 * @package    Com_Tjnotification
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

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
		parent::save($data);
		$input = JFactory::getApplication()->input;
		$cid      = $input->post->get('cid', array(), 'array');
		$recordId = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$msg = JText::_('COM_TJNOTIFICATIONS_FIELD_CREATED_SUCCESSFULLY');
		$link = JRoute::_(
		'index.php?option=com_tjnotifications&view=notification&layout=edit&id= ' . $recordId .
		'&extension=' . $input->get('extension', '', 'STRING'), false
		);
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
		parent::save($data);
		$msg = JText::_('COM_TJNOTIFICATIONS_FIELD_CREATED_SUCCESSFULLY');
		$input = JFactory::getApplication()->input;
		$link = JRoute::_(
		'index.php?option=com_tjnotifications&view=notifications&extension=' . $input->get('extension', '', 'STRING'), false
		);
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
		$input    = JFactory::getApplication()->input;
		$cid      = $input->post->get('cid', array(), 'array');
		$recordId = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$link = JRoute::_(
		'index.php?option=com_tjnotifications&view=notification&layout=edit&id= ' . $recordId .
		'&extension=' . $input->get('extension', '', 'STRING'), false
		);
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
		$input = JFactory::getApplication()->input;
		$link = JRoute::_('index.php?option=com_tjnotifications&view=notifications&extension=' . $input->get('extension', '', 'STRING'), false
		);
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
		$input = JFactory::getApplication()->input;
		$cid      = $input->post->get('cid', array(), 'array');
		$recordId = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$link = JRoute::_(
		'index.php?option=com_tjnotifications&view=notification&layout=edit&extension=' . $input->get('extension', '', 'STRING'), false
		);
		$this->setRedirect($link);
	}
}
