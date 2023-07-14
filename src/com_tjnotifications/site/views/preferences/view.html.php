<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;

/**
 * Content categories view.
 *
 * @since  1.6
 */
class TJNotificationsViewPreferences extends \Joomla\CMS\MVC\View\HtmlView
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name  of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */

	public function display($tpl = null)
	{
		// Get data from the model

		$user = Factory::getUser();

		if ($user->id)
		{
			$this->pagination	= $this->get('Pagination');
			$this->clients		= $this->get('Client');
			$model = $this->getModel();

			for ($i = 0; $i < count($this->clients); $i++)
			{
				$this->keys[$this->clients[$i]->client] = $model->Keys($this->clients[$i]->client);
			}

			$this->preferences = $this->get('States');
			$model = ListModel::getInstance('Providers', 'TJNotificationsModel');
			$this->providers	= $model->getProvider();
			$modelPreferences = ListModel::getInstance('Preferences', 'TJNotificationsModel');

			for ($i = 0;$i < count($this->providers); $i++)
			{
				$this->adminPreferences[$this->providers[$i]->provider] = $modelPreferences->adminPreferences($this->providers[$i]->provider);
			}
			parent::display($tpl);
		}
		else
		{
			$message = Text::sprintf('JGLOBAL_YOU_MUST_LOGIN_FIRST');
			$app     = Factory::getApplication();
			$app->enqueueMessage($message);
			$app->redirect(Route::_(Uri::root()));
		}
	}
}
