<div class="translations index">
<h2><?php __('Translations');?></h2>
<p>
<?php
echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
));
?></p>
<div>

<?php
	if (CakePlugin::loaded('Search')) {
		echo $this->Form->create('Translation', array('action' => 'index'));

		echo $this->Form->input("locale", array(
				'type' => 'text',
				'label' => __('Locale Name', true)));
		echo $this->Form->input("model", array(
				'type' => 'text',
				'label' => __('Model Name', true)));
		echo $this->Form->input("field", array(
				'type' => 'text',
				'label' => __('Field', true)));
		echo $this->Form->input("content", array(
				'type' => 'text',
				'label' => __('Content', true)));

		echo $this->Form->end(__('Search', true));
	}

?>
</div>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('locale');?></th>
	<th><?php echo $this->Paginator->sort('model');?></th>
	<th><?php echo $this->Paginator->sort('foreign_key');?></th>
	<th><?php echo $this->Paginator->sort('field');?></th>
	<th><?php echo $this->Paginator->sort('content');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($translations as $translation):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $translation['Translation']['locale']; ?>
		</td>
		<td>
			<?php echo $translation['Translation']['model']; ?>
		</td>
		<td>
			<?php echo $translation['Translation']['foreign_key']; ?>
		</td>
		<td>
			<?php echo $translation['Translation']['field']; ?>
		</td>
		<td>
			<?php echo $translation['Translation']['content']; ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $translation['Translation']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $translation['Translation']['id'])); ?>
			<?php echo $this->Html->link(__('Edit All locales for this entry', true), array('action' => 'edit_multi', $translation['Translation']['model'], $translation['Translation']['foreign_key'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $translation['Translation']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->element('Templates.paging');?>
</div>

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('New Translation', true), array('action' => 'add')); ?></li>
	</ul>
</div>
