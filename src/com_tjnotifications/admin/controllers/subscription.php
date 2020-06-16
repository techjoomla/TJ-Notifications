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

/**
 * Controller for form for subscription
 *
 * @package  Tjnotifications
 *
 * @since    __DEPLOY_VERSION__
 */
class TjnotificationsControllerSubscription extends FormController
{
	/**
	 * The extension for which the subscription apply.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $extension;

	protected $view_list;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'subscriptions';

		// Guess the extension
		if (empty($this->extension))
		{
			$this->extension = Factory::getApplication()->input->getCmd('extension', '');
		}

		parent::__construct();
	}

	/**
	 * Save subscription data
	 *
	 * @param   integer  $key     key.
	 *
	 * @param   integer  $urlVar  url
	 *
	 * @return  boolean|string  The arguments to append to the redirect URL.
	 *
	 * @since   2.3.0
	 */
	public function save($key = null, $urlVar = '')
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = Factory::getApplication();
		$input = $app->input;
		$model = $this->getModel('Subscription', 'TjnotificationsModel');
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
				$this->setRedirect(
					Route::_(
						'index.php?option=com_tjnotifications&view=subscription' . $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
				);

				return false;
			}

			// Reset the ID, the multilingual associations and then treat the request as for Apply.
			$data[$key] = 0;

			$data['title'] = 'Copy of ' . $data['title'];

			$task = 'apply';
		}

		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				Route::_(
					'index.php?option=com_tjnotifications&view=subscription' . $this->getRedirectToListAppend(),
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
			$app->setUserState('com_tjnotifications.edit.subscription.data', $data);

			// Redirect back to the edit screen
			$this->setRedirect(Route::_('index.php?option=com_tjnotifications&view=subscription' . $this->getRedirectToItemAppend($recordId, $urlVar), false));

			$this->redirect();
		}

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState('com_tjnotifications.edit.subscription.data', $data);

			// Redirect back to the edit screen.
			$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(Route::_('index.php?option=com_tjnotifications&view=subscription' . $this->getRedirectToItemAppend($recordId, $urlVar)), false);

			return false;
		}

		// Save succeeded, so check-in the record.
		if ($checkin && $model->checkin($validData['id']) === false)
		{
			// Save the data in the session.
			$app->setUserState('com_tjnotifications.edit.subscription.data', $validData);

			// Check-in failed, so go back to the record and display a notice.
			$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(Route::_('index.php?option=com_tjnotifications&view=subscription' . $this->getRedirectToItemAppend($recordId, $urlVar), false));

			return false;
		}

		$this->setMessage(Text::_('COM_TJNOTIFICATIONS_MSG_SUCCESS_SAVE_SUBSCRIPTION'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the record data in the session.
				$recordId = $model->getState('com_tjnotifications.edit.subscription.id');
				$this->holdEditId('com_tjnotifications.edit.subscription', $recordId);
				$app->setUserState('com_tjnotifications.edit.subscription.data', null);
				$model->checkout($recordId);

				// Redirect back to the edit screen.
				$this->setRedirect(
					Route::_(
						'index.php?option=com_tjnotifications&view=subscription' . $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
				);
			break;

			case 'save2new':
				// Clear the record id and data from the session.
				$this->releaseEditId('com_tjnotifications.edit.subscription', $recordId);
				$app->setUserState('com_tjnotifications.edit.subscription.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(Route::_('index.php?option=com_tjnotifications&view=subscription' . $this->getRedirectToItemAppend(null, $urlVar), false));
			break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId('com_tjnotifications.edit.subscription', $recordId);
				$app->setUserState('com_tjnotifications.edit.subscription.data', null);

				$url = 'index.php?option=com_tjnotifications&view=subscriptions' . $this->getRedirectToListAppend();

				// Check if there is a return value
				$return = $this->input->get('return', null, 'base64');

				if (!is_null($return) && Uri::isInternal(base64_decode($return)))
				{
					$url = base64_decode($return);
				}

				// Redirect to the list screen.
				$this->setRedirect(Route::_($url, false));

			break;
		}
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&extension=' . $this->extension;

		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&extension=' . $this->extension;

		return $append;
	}
}
