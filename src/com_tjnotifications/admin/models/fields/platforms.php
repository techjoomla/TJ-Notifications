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

use Joomla\CMS\Language\Text;

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of platforms
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldPlatforms extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var	 string
	 * @since   __DEPLOY_VERSION__
	 */
	protected $type = 'platforms';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array   An array of JHtml options.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		$options = array();

		$options[] = JHtml::_('select.option', '',        Text::_('COM_TJNOTIFICATIONS_PLATFORM_LIST_CHOOSE'));
		$options[] = JHtml::_('select.option', 'android', Text::_('COM_TJNOTIFICATIONS_PLATFORM_ANDROID'));
		$options[] = JHtml::_('select.option', 'ios',     Text::_('COM_TJNOTIFICATIONS_PLATFORM_IOS'));
		$options[] = JHtml::_('select.option', 'web',     Text::_('COM_TJNOTIFICATIONS_PLATFORM_WEB'));

		return array_merge(parent::getOptions(), array_values($options));
	}

	/**
	 * Method to get a list of options for a list input externally and not from xml.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   2.1
	 */
	public function getOptionsExternally()
	{
		return $this->getOptions();
	}
}
