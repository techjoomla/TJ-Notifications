<?php
/**
 * @package     Tjnotifications
 * @subpackage  tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;

/**
 * Controller for form for message
 *
 * @package  Tjnotifications
 *
 * @since    2.1.0
 */
class TjnotificationsControllerMessageform extends FormController
{
	/**
	 * Method to remove data
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.1.0
	 */
	public function markAsRead()
	{
		$app    = Factory::getApplication();
		$model  = $this->getModel('MessageForm', 'TjnotificationsModel');
		$pk     = $app->input->post->get('id');
		$result = array();

		if (empty($pk))
		{
			$result['success'] = false;
			$result['message'] = Text::_('COM_TJNOTIFICATIONS_ERROR_NO_ID_PASSED');
		}
		else
		{
			// Attempt to update data
			try
			{
				$model->markAsRead($pk);

				$result['success'] = true;
			}
			catch (Exception $e)
			{
				$result['success'] = false;
				$result['message'] = Text::_($e->getMessage());
			}
		}

		echo json_encode($result);
		jexit();
	}
}
