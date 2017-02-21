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

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_CLIENT"), 'client', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_KEY"), 'key', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_EMAIL_STATUS"), 'email_status', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_SMS_STASTUS"), 'sms_status', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_PUSH_STATUS"), 'push_status', $listDirn, $listOrder);?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_WEB_STATUS"), 'web_status', $listDirn, $listOrder);?>
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

									<td class="center">
										<a href="<?php echo $link; ?>">
											<?php echo $row->client; ?>
										</a>
									</td>

									<td class="center">
									<a href="<?php echo $link;  ?>">
											<?php echo $row->key; ?>
										</a>
									</td>
									<?php $tick='components/com_tjnotifications/images/tick.png';
									$no='components/com_tjnotifications/images/n.png'; ?>
									<td class="center">
										<?php if(($row->email_status)==1): ?>
										<?php $image = JURI::base(). $tick;?>
										<?php printf('<img src="%s" align="center"/>', $image); ?>
										<?php else :?>
										<?php $image = JURI::base(). $no;?>
										<?php printf('<img src="%s" />', $image); ?>
										<?php endif; ?>
									</td>
									<td class="center">
										<?php if(($row->sms_status)==1): ?>
										<?php $image = JURI::base(). $tick;?>
										<?php printf('<img src="%s" />', $image); ?>
										<?php else :?>
										<?php $image = JURI::base(). $no;?>
										<?php printf('<img src="%s" />', $image); ?>
										<?php endif; ?>
									</td>
									<td class="center">
										<?php if(($row->push_status)==1): ?>
										<?php $image = JURI::base(). $tick;?>
										<?php printf('<img src="%s" />', $image); ?>
										<?php else :?>
										<?php $image = JURI::base(). $no;?>
										<?php printf('<img src="%s" />', $image); ?>
										<?php endif; ?>
									</td>
									<td class="center">
										<?php if(($row->web_status)==1): ?>
										<?php $image = JURI::base(). $tick;?>
										<?php printf('<img src="%s" />', $image); ?>
										<?php else :?>
										<?php $image = JURI::base(). $no;?>
										<?php printf('<img src="%s" />', $image); ?>
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
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>


