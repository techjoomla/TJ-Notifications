<?php
/**
 * @package     Tjnotifications
 * @subpackage  com_tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http:/www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

/**
 * Notification controller class.
 *
 * @since  0.0.1
 */
class TjnotificationsControllerNotification extends FormController
{
	/**
	 * Save notification data
	 *
	 * @param   integer  $key     key.
	 *
	 * @param   integer  $urlVar  url
	 *
	 * @return  boolean|string  The arguments to append to the redirect URL.
	 */
	public function save($key = null, $urlVar = '')
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = Factory::getApplication();
		$input = $app->input;
		$model = $this->getModel('Notification', 'TjnotificationsModel');
		$table = $model->getTable();

		$data = $input->post->get('jform', array(), 'array');
		$task = $this->getTask();

		$checkin = property_exists($table, $table->getColumnAlias('checked_out'));

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		$recordId = $this->input->getInt($urlVar);

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// The save2copy task needs to be handled slightly differently.
		if ($task === 'save2copy')
		{
			// Check-in the original row.
			if ($checkin && $model->checkin($data[$key]) === false)
			{
				// Check-in failed. Go back to the item and display a notice.
				$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'error');
				$extension = $input->get('extension', '', 'STRING');

				// Redirect back to the edit screen.
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

				return false;
			}

			// Reset the ID, the multilingual associations and then treat the request as for Apply.
			$data[$key] = 0;

			$task = 'apply';
		}

		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				Route::_(
					'index.php?option=com_tjnotifications&view=notification' . $this->getRedirectToListAppend(),
					false
				)
			);

			return false;
		}

		// Get form
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Validate the posted data.
		$validData = $model->validate($form, $data);

		// Check for errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
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

			// Save the data in the session.
			$app->setUserState('com_tjnotifications.edit.notification.data', $data);

			// Redirect back to the edit screen
			$extension = $input->get('extension', '', 'STRING');

			// Redirect back to the edit screen.
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

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState('com_tjnotifications.edit.notification.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($model->getError(), 'error');

			$extension = $input->get('extension', '', 'STRING');

			// Redirect back to the edit screen.
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

			return false;
		}

		// Save succeeded, so check-in the record.
		if ($checkin && $model->checkin($validData['id']) === false)
		{
			// Save the data in the session.
			$app->setUserState('com_tjnotifications.edit.notification.data', $validData);

			// Check-in failed, so go back to the record and display a notice.
			$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$extension = $input->get('extension', '', 'STRING');

			// Redirect back to the edit screen.
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

			return false;
		}

		$this->setMessage(Text::_('COM_TJNOTIFICATIONS_FIELD_CREATED_SUCCESSFULLY'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the record data in the session.
				$recordId = $model->getState('com_tjnotifications.edit.notification.id');
				$this->holdEditId('com_tjnotifications.edit.notification', $recordId);
				$app->setUserState('com_tjnotifications.edit.notification.data', null);
				$model->checkout($recordId);

				$extension = $input->get('extension', '', 'STRING');

				// Redirect back to the edit screen.
				if ($extension)
				{
					$link = Route::_(
					'index.php?option=com_tjnotifications&view=notification' . $this->getRedirectToItemAppend($recordId, $urlVar) .
					'&extension=' . $extension, false
					);
				}
				else
				{
					$link = Route::_(
					'index.php?option=com_tjnotifications&view=notification' . $this->getRedirectToItemAppend($recordId, $urlVar), false
					);
				}

				$this->setRedirect($link);

			break;

			case 'save2new':
				// Clear the record id and data from the session.
				$this->releaseEditId('com_tjnotifications.edit.notification', $recordId);
				$app->setUserState('com_tjnotifications.edit.notification.data', null);

				// Redirect back to the edit screen.
				$extension = $input->get('extension', '', 'STRING');

				// Redirect back to the edit screen.
				if ($extension)
				{
					$link = Route::_(
					'index.php?option=com_tjnotifications&view=notification' . $this->getRedirectToItemAppend(null, $urlVar) . '&extension=' . $extension, false
					);
				}
				else
				{
					$link = Route::_(
					'index.php?option=com_tjnotifications&view=notification' . $this->getRedirectToItemAppend(null, $urlVar), false
					);
				}

				$this->setRedirect($link);
			break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId('com_tjnotifications.edit.notification', $recordId);
				$app->setUserState('com_tjnotifications.edit.notification.data', null);

				$extension = $input->get('extension', '', 'STRING');

				// Redirect back to the notification list view.
				if ($extension)
				{
					$link = Route::_(
					'index.php?option=com_tjnotifications&view=notifications' . $this->getRedirectToListAppend() . '&extension=' . $extension, false
					);
				}
				else
				{
					$link = Route::_(
					'index.php?option=com_tjnotifications&view=notifications' . $this->getRedirectToListAppend(), false
					);
				}

				// Check if there is a return value
				$return = $this->input->get('return', null, 'base64');

				if (!is_null($return) && Uri::isInternal(base64_decode($return)))
				{
					$link = base64_decode($return);
				}

				// Redirect to the list screen.
				$this->setRedirect(Route::_($link, false));

			break;
		}
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
	public function add($key = null, $urlVar = null)
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$input     = Factory::getApplication()->input;
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
