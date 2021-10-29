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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

// Add the defines.php file
require_once JPATH_COMPONENT . '/defines.php';

HTMLHelper::script('media/com_tjnotifications/js/tjnotifications.min.js');

$controller = BaseController::getInstance('Tjnotifications');

// Perform the Request task
$input = Factory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
