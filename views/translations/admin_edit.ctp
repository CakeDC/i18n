<div class="translations form">
<?php echo $this->Form->create('Translation', array('url' => array('action' => 'edit')));?>
	<fieldset>
 		<legend><?php __('Admin Edit Translation');?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('locale');
		echo $this->Form->input('model');
		echo $this->Form->input('foreign_key');
		echo $this->Form->input('field');
		echo $this->Form->input('content');
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Translation.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Translations', true), array('action' => 'index'));?></li>
	</ul>
</div>