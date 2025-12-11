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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;

$preferencesPath = JPATH_SITE . '/components/com_tjnotifications/models/preferences.php';
if (file_exists($preferencesPath)) {
	require_once $preferencesPath;
}

/**
 * new notification View
 *
 * @since  0.0.1
 */
class TjnotificationsViewNotification extends HtmlView
{
	/**
	 * View form
	 *
	 * @var         form
	 */
	protected $form = null;

	protected $state;

	protected $item;

	public $app;

	public $user;

	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Validate
		$this->app           = Factory::getApplication();
		$this->user          = Factory::getUser();

		if (empty($this->user->authorise('core.create', 'com_tjnotifications')) || empty($this->user->authorise('core.edit', 'com_tjnotifications')))
		{
			$msg = Text::_('JERROR_ALERTNOAUTHOR');
			throw new \Exception($msg, 403);
			$this->app->redirect(Route::_('index.php?Itemid=0', false));
		}

		/*$this->items       = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');*/

		// Get data from the model
		$this->state     = $this->get('State');
		$this->form      = $this->get('Form');
		$this->item      = $this->get('Item');
		$this->component = $this->state->get('filter.component');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode('<br />', $errors), 500);

			return false;
		}

		$this->addToolbar();

		$extension  = $this->app->input->getCmd('extension', '');

		if ($extension)
		{
			$this->_setToolbar();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $this->user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}

		ToolbarHelper::title(Text::_('COM_TJNOTIFICATIONS'), 'edit.png');

		// If not checked out, can save the item.
		if (!$checkedOut)
		{
			ToolbarHelper::apply('notification.apply', 'JTOOLBAR_APPLY');
			ToolbarHelper::save('notification.save', 'JTOOLBAR_SAVE');
			ToolbarHelper::custom('notification.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew)
		{
			ToolbarHelper::custom('notification.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))
		{
			ToolbarHelper::cancel('notification.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolbarHelper::cancel('notification.cancel', 'JTOOLBAR_CLOSE');
		}
	}

	/**
	 * Function to set tool bar.
	 *
	 * @return void
	 *
	 * @since	1.8
	 */
	public function _setToolbar()
	{
		$component  = $this->state->get('filter.component');
		$section    = $this->state->get('filter.section');

		// Avoid nonsense situation.
		if ($component == 'com_notifications')
		{
			return;
		}
		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = Factory::getLanguage();
		$lang->load($component, JPATH_BASE, null, false, true)
		|| $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component, null, false, true);

		// If a component notification title string is present, let's use it.
		if ($lang->hasKey($component_title_key = strtoupper($component . ($section ? "_$section" : '')) . '_NOTIFICATIONS_TEMPLATES'))
		{
			$title = Text::_($component_title_key);
		}
		elseif ($lang->hasKey($component_section_key = strtoupper($component . ($section ? "_$section" : ''))))
		// Else if the component section string exits, let's use it
		{
			$title = Text::sprintf('COM_TJNOTIFICATIONS_NOTIFICATION_TITLE', $this->escape(Text::_($component_section_key)));
		}
		else
		// Else use the base title
		{
			$title = Text::_('COM_TJNOTIFICATIONS_NOTIFICATION_BASE_TITLE');
		}

		// Prepare the toolbar.
		ToolbarHelper::title($title, 'folder notifications ' . substr($component, 4) . ($section ? "-$section" : '') . '-notification templates');
	}
}
