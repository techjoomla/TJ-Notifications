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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * View for list of subscription
 *
 * @package  Tjnotifications
 *
 * @since    2.0.0
 */
class TjnotificationsViewSubscriptions extends HtmlView
{
	protected $activeFilters;

	protected $extension;

	public $filterForm;

	protected $input;

	protected $items;

	protected $pagination;

	protected $state;

	public $sidebar;

	protected $user;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->user          = Factory::getUser();
		$this->input         = Factory::getApplication()->input;
		$this->extension     = $this->input->getCmd('extension', '');

		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		TjnotificationsHelper::addSubmenu('subscriptions');

		$this->addToolbar();

		// Joomla 6 compatible sidebar rendering
		$this->sidebar = $this->renderSidebar();
		
		parent::display($tpl);
	}

	/**
	 * Render the sidebar for Joomla 6
	 *
	 * @return string  The rendered sidebar HTML
	 *
	 * @since  2.0.0
	 */
	protected function renderSidebar()
	{
		// Joomla 4+ doesn't use sidebars in the same way as Joomla 3
		// The addSubmenu() method handles submenu registration
		// Return empty string as sidebar is handled by Joomla core
		return '';
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = ContentHelper::getActions('com_tjnotifications', '', 0);

		// Updated for Joomla 6 - removed .png extension from icon
		ToolbarHelper::title(Text::_('COM_TJNOTIFICATIONS_SUBSCRIPTIONS_PAGE_TITLE'), 'list');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/subscription';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				// Updated for Joomla 6 - removed second parameter
				ToolbarHelper::addNew('subscription.add');

				/*if (isset($this->items[0]))
				{
					ToolbarHelper::custom('subscription.duplicate', 'copy', '', 'TOOLBAR_DUPLICATE', true);
				}*/
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				// Updated for Joomla 6 - removed second parameter
				ToolbarHelper::editList('subscription.edit');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				ToolbarHelper::divider();
				// Updated for Joomla 6 - removed icon parameters
				ToolbarHelper::publish('subscriptions.publish', 'JTOOLBAR_PUBLISH', true);
				ToolbarHelper::unpublish('subscriptions.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				ToolbarHelper::deleteList('', 'subscriptions.delete', 'JTOOLBAR_DELETE');
			}

			/*if (isset($this->items[0]->state))
			{
				ToolbarHelper::divider();
				ToolbarHelper::archiveList('subscriptions.archive');
			}*/

			if (isset($this->items[0]->checked_out))
			{
				// Updated for Joomla 6 - removed icon parameters
				ToolbarHelper::checkin('subscriptions.checkin', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				ToolbarHelper::deleteList('', 'subscriptions.delete', 'JTOOLBAR_EMPTY_TRASH');
				ToolbarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				ToolbarHelper::trash('subscriptions.trash');
				ToolbarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			ToolbarHelper::preferences('com_tjnotifications');
		}

		// Note: Sidebar action setting removed as HTMLHelperSidebar is deprecated in Joomla 6
		// If you need to set action for filters, handle it in the form XML or template
	}

	/**
	 * Method to order fields
	 *
	 * @return array
	 */
	protected function getSortFields()
	{
		return array(
			'a.id'       => Text::_('JGRID_HEADING_ID'),
			'a.state'    => Text::_('JSTATUS'),
			'a.name'     => Text::_('COM_TJNOTIFICATIONS_SUBSCRIPTIONS_NAME'),
		);
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return boolean
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}
}