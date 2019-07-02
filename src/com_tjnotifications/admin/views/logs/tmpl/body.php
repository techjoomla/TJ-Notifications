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
use Joomla\CMS\Table\Table;

$jinput  = Factory::getApplication()->input;
$logId = $jinput->get('id', 0, 'INT');

if ($logId)
{
	Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');
	$logTable = Table::getInstance('Log', 'TjnotificationsTable');
	$logTable->load(array('id' => $logId));
}
?>
<div>
	<?php if ($logTable->id) : ?>
	<h3 class="modal-title">
		<?php echo Text::_("COM_TJNOTIFICATIONS_FIELD_NOTIFICATION_BODY_LABEL"); ?>
	 </h3>
	<div class="col-xs-12">
		<?php echo $logTable->body; ?>
	</div>
	<?php endif; ?>
</div>

