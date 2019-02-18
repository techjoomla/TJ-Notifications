<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jticketing
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

JHtml::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
?>

<form action="index.php?option=com_tjnotifications&view=notifications" method="post" id="adminForm" name="adminForm">

	<?php
		if (!empty($this->sidebar)):?>
				<div id="j-sidebar-container" class="span2">
					<?php echo $this->sidebar;?>
				</div>
				<div id="j-main-container" class="span10">
		<?php else :?>
				<div id="j-main-container">
		<?php endif;?>

	<div class="row-fluid">
		<div class="span10">
			<?php
				echo JLayoutHelper::render(
					'joomla.searchtools.default',
					array('view' => $this)
				);
			?>
		</div>
	</div>

	<div class="btn-group pull-right hidden-phone">
		<label for="limit" class="element-invisible">
			<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
		</label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>

	<br>
		<div class="clearfix"></div>
		<?php if(empty($this->items)) :?>
			<div class="clearfix">&nbsp;</div>
				<div class="alert alert-no-items">
					<?php echo JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_NO_MATCHING_RESULTS"); ?>
			</div>
		<?php else :?>
				<table class="table table-striped table-hover">
					<thead>
					<tr>

						<th width="2%" class="nowrap center">
							<input type="checkbox" name="checkall-toggle" value=""
										   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>

						<th width="10%" class="hidden-phone">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_TITLE"), 'title', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="hidden-phone">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_KEY"), 'key', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_EMAIL_STATUS"), 'email_status', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_USER_CONTROL"), 'user_control', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_UNSUBCRIBED_USERS");?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_CORE_TEMPLATE"), 'core', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_ID"), 'id', $listDirn, $listOrder); ?>
						</th>

					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="12">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<?php if (!empty($this->items)) : ?>
							<?php foreach ($this->items as $i => $row) :
							if($this->component):
								$link = JRoute::_('index.php?option=com_tjnotifications&task=notification.edit&id=' . $row->id.'&extension=' . $this->component);
							else :
								$link = JRoute::_('index.php?option=com_tjnotifications&task=notification.edit&id=' . $row->id);
							endif;
							?>
								<tr>
									<td class="center">
										<?php echo JHtml::_('grid.id', $i, $row->id); ?>
									</td>

									<td class="">
										<a href="<?php echo $link; ?>">
											<?php echo $row->title; ?>
										</a>
									</td>

									<td class="">
									<a href="<?php echo $link;  ?>">
											<?php echo $row->key; ?>
										</a>
									</td>

									<td class="center">
										<a href="javascript:void(0);" class="hasTooltip" data-original-title="<?php echo ( $row->email_status ) ? JText::_( 'COM_TJNOTIFICATIONS_STATE_ENABLE' ) : JText::_( 'COM_TJNOTIFICATIONS_STATE_DISABLE' );?>" onclick=" listItemTask('cb<?php echo $i;?>','<?php echo ( $row->email_status ) ? 'notifications.disableEmailStatus' : 'notifications.enableEmailStatus';?>')">
											<img src="<?php echo JUri::root();?>administrator/components/com_tjnotifications/images/<?php echo ( $row->email_status ) ? 'publish.png' : 'unpublish.png';?>" width="16" height="16" border="0" />
										</a>
									</td>

									<td class="center">
										<a href="javascript:void(0);" class="hasTooltip" data-original-title="<?php echo ( $row->user_control ) ? JText::_( 'COM_TJNOTIFICATIONS_STATE_ENABLE' ) : JText::_( 'COM_TJNOTIFICATIONS_STATE_DISABLE' );?>" onclick=" listItemTask('cb<?php echo $i;?>','<?php echo ( $row->user_control ) ? 'notifications.disableUserControl' : 'notifications.enableUserControl';?>')">
											<img src="<?php echo JUri::root();?>administrator/components/com_tjnotifications/images/<?php echo ( $row->user_control ) ? 'publish.png' : 'unpublish.png';?>" width="16" height="16" border="0" />
										</a>
									</td>

									<td class="center">
										<?php echo $this->count->name; ?>
									</td>

									<td class ="center">
										<?php if ($row->core):?>
											<span class="label label-important"><?php echo JText::_("COM_TJNOTIFICATIONS_CORE_TEMPLATE_CORE_VALUE")?></span>
										<?php else :?>
											<span class="label"><?php echo JText::_("COM_TJNOTIFICATIONS_CORE_TEMPLATE_VALUE")?></span>
										<?php endif; ?>
									</td>

									<td class="center">
										<a href="<?php echo $link; ?>">
											<?php echo $row->id; ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
		<?php endif ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="extension" value="<?php echo $this->component; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
