<div class="translations form">
<?php echo $this->Form->create('Translation', array('url' => array('action' => 'edit_multi', $model, $foreignKey)));?>
	<fieldset>
 		<legend><?php __('Admin Edit Translation');?></legend>
	<?php
		echo '<h3>' . $model . '</h3>';

		foreach ($translations as $translation) {
			$field = $translation['Translation']['field'];
			$locale = $translation['Translation']['locale'];
			$id = $translation['Translation']['id'];
			echo $this->Form->hidden("Translation.$locale.$field.id", array(
				'value' => $translation['Translation']['id']));
			echo $this->Form->input("Translation.$locale.$field.content",  array(
				'type' => 'string',
				'label' => sprintf('Field %s, locale %s', $field, $locale),
				'value' => $translation['Translation']['content']));
		}

	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List Translations', true), array('action' => 'index'));?></li>
	</ul>
</div>