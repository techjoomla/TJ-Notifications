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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

/**
 * Notification controller class.
 *
 * @since  0.0.1
 */
class TjnotificationsControllerNotification extends \Joomla\CMS\MVC\Controller\FormController
{
	/**
	 * Function to add field data
	 *
	 * @return  void
	 */
	public function editSave()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
		$app   = Factory::getApplication();

		$input     = $app->input;
		$data  = $input->post->get('jform', array(), 'array');
		$model     = $this->getModel('Notification', 'TjnotificationsModel');

		$form = $model->getForm();

		if (!$form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		// Validate the posted data.
		$return = $model->validate($form, $data);

		if ($return == false)
		{
			$errors = $model->getErrors();

			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$cid       = $input->get('cid', array(), 'post', 'array');
			$recordId  = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		}
		else
		{
			$recordId = $model->save($data);

			if ($recordId === false)
			{
				$msg = $app->enqueueMessage(Text::_('COM_TJNOTIFICATIONS_MODEL_NOTIFICATION_KEY_DUPLICATE_MESSAGE'), 'error');
			}
			else
			{
				$msg = Text::_('COM_TJNOTIFICATIONS_FIELD_CREATED_SUCCESSFULLY');
			}
		}

		$extension = $input->get('extension', '', 'STRING');

		if ($extension)
		{
			$link = Route::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit&id=' . $recordId .
			'&extension=' . $extension, false
			);
		}
		else
		{
			$link = Route::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit&id=' . $recordId, false
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
	public function saveClose($key = null, $urlVar = null)
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
		$app   = Factory::getApplication();

		$input     = $app->input;
		$extension = $input->get('extension', '', 'STRING');
		$model     = $this->getModel('Notification', 'TjnotificationsModel');
		$data  = $input->post->get('jform', array(), 'array');

		$form = $model->getForm();

		if (!$form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		// Validate the posted data.
		$return = $model->validate($form, $data);

		if ($return == false)
		{
			$errors = $model->getErrors();

			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$cid       = $input->get('cid', array(), 'post', 'array');
			$recordId  = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		}
		else
		{
			$recordId = $model->save($data);

			if ($recordId === false)
			{
				$msg = $app->enqueueMessage(Text::_('COM_TJNOTIFICATIONS_MODEL_NOTIFICATION_KEY_DUPLICATE_MESSAGE'), 'error');
			}
			else
			{
				$msg = Text::_('COM_TJNOTIFICATIONS_FIELD_CREATED_SUCCESSFULLY');
			}
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
		$input = Factory::getApplication()->input;
		$user  = Factory::getUser();

		if (empty($user->authorise('core.edit', 'com_tjnotifications')))
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$cid       = $input->get('cid', array(), 'post', 'array');
		$recordId  = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$extension = $input->get('extension', '', 'STRING');

		if ($extension)
		{
			$link = Route::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit&id=' . $recordId .
			'&extension=' . $extension, false
			);
		}
		else
		{
			$link = Route::_(
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
		$input     = Factory::getApplication()->input;
		$extension = $input->get('extension', '', 'STRING');

		if ($extension)
		{
			$link = Route::_('index.php?option=com_tjnotifications&view=notifications&extension=' . $extension, false
			);
		}
		else
		{
			$link = Route::_('index.php?option=com_tjnotifications&view=notifications', false
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
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$app   = Factory::getApplication();
		$input    = $app->input;
		$extension = $input->get('extension', '', 'STRING');
		$model     = $this->getModel('Notification', 'TjnotificationsModel');
		$data  = $input->post->get('jform', array(), 'array');
		$form = $model->getForm();

		if (!$form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		// Validate the posted data.
		$return = $model->validate($form, $data);

		if ($return == false)
		{
			$errors = $model->getErrors();

			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$cid       = $input->get('cid', array(), 'post', 'array');
			$recordId  = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		}
		else
		{
			$recordId = $model->save($data);

			if ($recordId === false)
			{
				$msg = $app->enqueueMessage(Text::_('COM_TJNOTIFICATIONS_MODEL_NOTIFICATION_KEY_DUPLICATE_MESSAGE'), 'error');
			}
			else
			{
				$msg = Text::_('COM_TJNOTIFICATIONS_FIELD_CREATED_SUCCESSFULLY');
			}
		}

		if ($extension)
		{
			$link = Route::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit&extension=' . $extension, false
			);
		}
		else
		{
			$link = Route::_(
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
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$input     = Factory::getApplication()->input;
		$cid       = $input->post->get('cid', array(), 'array');
		$recordId  = (int) (count($cid) ? $cid[0] : $input->getInt('id'));
		$extension = $input->get('extension', '', 'STRING');

		if ($extension)
		{
			$link = Route::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit&extension=' . $extension, false
			);
		}
		else
		{
			$link = Route::_(
			'index.php?option=com_tjnotifications&view=notification&layout=edit', false
			);
		}

		$this->setRedirect($link);
	}
}
