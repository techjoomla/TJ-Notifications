<?php
/**
 * @package    Com_Tjnotifications
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

$app  = Factory::getApplication();
$jinput  = Factory::getApplication()->input;
$logId = $jinput->get('id', 0, 'INT');

if(!empty($logId))
{
	Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');
	$logTable = Table::getInstance('Log', 'TjnotificationsTable');
	$logTable->load(array('id' => $logId));
}
else
{
	$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	return false;
}
?>
<div class="container">
	<?php if ($logTable->id) : ?>
	<h3 class="modal-title">
		<?php echo JText::_("COM_TJNOTIFICATIONS_FIELD_EMAIL_BODY_LABEL");
	  ?>
	 </h3>
	<div class="col-xs-12">
		<?php echo $logTable->params; ?>
	</div>
	<?php endif; ?>
</div>

