<div class="translations form">
<?php echo $this->Form->create('Translation', array('url' => array('action' => 'add')));?>
	<fieldset>
 		<legend><?php __('Admin Add Translation');?></legend>
	<?php
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
		<li><?php echo $this->Html->link(__('List Translations', true), array('action' => 'index'));?></li>
	</ul>
</div>