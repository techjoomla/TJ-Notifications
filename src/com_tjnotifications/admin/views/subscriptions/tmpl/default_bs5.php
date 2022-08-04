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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('bootstrap.tooltip');

$doc = Factory::getDocument();

$style     = "
.table th {
	white-space: inherit !important;
}";
$doc->addStyleDeclaration($style);

$userId    = $this->user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $this->user->authorise('core.edit.state', 'com_tjnotifications');

$sortFields = $this->getSortFields();
?>

<form action="<?php echo Route::_('index.php?option=com_tjnotifications&view=subscriptions&extension=' . $this->extension); ?>" method="post"
	name="adminForm" id="adminForm">
	<?php
	if (!empty($this->sidebar))
	{
		?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>

		<div id="j-main-container" class="span10">
		<?php
	}
	else
	{
		?>
		<div id="j-main-container">
		<?php
	}
	?>

			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

			<div class="clearfix"></div>
			<?php
			if (!empty($this->items))
			{
			?>
			<table class="table table-striped" id="subscriptionList">
				<thead>
					<tr>
						<th width="5%" class="center hidden-phone">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>

						<?php
						if (isset($this->items[0]->state))
						{
							?>
							<th width="5%" class="nowrap center">
								<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
							</th>
							<?php
						}
						?>

						<th width="15%" class="left">
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJNOTIFICATIONS_SUBSCRIPTIONS_USER', 'a.user_id', $listDirn, $listOrder); ?>
						</th>

						<th width="10%"class="left">
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJNOTIFICATIONS_SUBSCRIPTIONS_BACKEND', 'a.backend', $listDirn, $listOrder); ?>
						</th>

						<th width="30%" class="left">
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJNOTIFICATIONS_SUBSCRIPTIONS_ADDRESS', 'a.address', $listDirn, $listOrder); ?>
						</th>

						<th width="20%" class="left">
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJNOTIFICATIONS_SUBSCRIPTIONS_DEVICE_ID', 'a.device_id', $listDirn, $listOrder); ?>
						</th>

						<th width="10%" class="left">
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJNOTIFICATIONS_SUBSCRIPTIONS_IS_CONFIRMED', 'a.is_confirmed', $listDirn, $listOrder); ?>
						</th>

						<th width="5%" class="">
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_TJNOTIFICATIONS_SUBSCRIPTIONS_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>

				<tbody>
					<?php
					foreach ($this->items as $i => $item)
					{
						$ordering   = ($listOrder == 'a.id');
						$canCreate  = $this->user->authorise('core.create', 'com_tjnotifications');
						$canEdit    = $this->user->authorise('core.edit', 'com_tjnotifications');
						$canCheckin = $this->user->authorise('core.manage', 'com_tjnotifications');
						$canChange  = $this->user->authorise('core.edit.state', 'com_tjnotifications');
						?>

						<tr class="row<?php echo $i % 2; ?>">
							<td class="center hidden-phone" width="5%">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>

							<?php
							if (isset($this->items[0]->state))
							{
								?>
								<td class="center" width="5%">
									<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'subscriptions.', $canChange, 'cb'); ?>
								</td>
								<?php
							}
							?>

							<td width="15%">
								<?php
								if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange))
								{
									echo HTMLHelper::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'subscriptions.', $canCheckin);
								}

								if ($canEdit)
								{
									?>
									<a href="<?php echo Route::_('index.php?option=com_tjnotifications&task=subscription.edit&id=' . (int) $item->id) .
										'&extension=' . $this->extension; ?>">
										<?php echo Factory::getUser($item->user_id)->name; ?>
									</a>

									<br/>

									<span class="small">
										<?php echo $this->escape($item->title); ?>
									</span>

									<?php
								}
								else
								{
									echo Factory::getUser($item->user_id)->name;
									?>

									<br/>

									<span class="small">
										<?php echo $this->escape($item->title); ?>
									</span>

									<?php
								}
								?>
							</td>

							<td width="10%"><?php echo $item->backend; ?></td>
							<td width="30%"><?php echo $item->address; ?></td>

							<td width="20%">
								<?php echo $item->device_id; ?>

								<?php
								if (!empty($item->platform))
								{
									?>
									<br/>
									<span class="small">
										<?php echo Text::_('COM_TJNOTIFICATIONS_SUBSCRIPTIONS_PLATFORM') . ': ' . $item->platform; ?>
									</span>
									<?php
								}
								?>
							</td>

							<td width="10%">
								<?php echo ($item->is_confirmed) ? Text::_('JYES') : Text::_('JNO'); ?>
							</td>

							<td width="5%"><?php echo $item->id; ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>

				<tfoot>
					<tr>
						<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
			<?php
			}
			else
			{
				?>
				<div class="alert alert-info">
					<?php echo Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_NO_MATCHING_RESULTS"); ?>
				</div>
				<?php
			}
			?>
			<input type="hidden" name="extension"  value="<?php echo $this->extension; ?>"/>
			<input type="hidden" name="task"       value=""/>
			<input type="hidden" name="boxchecked" value="0"/>

			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
</form>
