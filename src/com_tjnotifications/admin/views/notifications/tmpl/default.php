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

if (version_compare(phpversion(), '7.4.0', '<'))
{
	echo $this->loadTemplate('php5');
}
else
{
	echo $this->loadTemplate('php7');
}
