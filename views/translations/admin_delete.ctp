<h2><?php echo sprintf(__('Delete Translation "%s"?', true), $translation['Translation']['title']); ?></h2>
<p>	
	<?php __('Be aware that your Translation and all associated data will be deleted if you confirm!'); ?>
</p>
<?php
	echo $this->Form->create('Translation', array(
		'url' => array(
			'action' => 'delete',
			$translation['Translation']['id'])));
	echo $form->input('confirm', array(
		'label' => __('Confirm', true),
		'type' => 'checkbox',
		'error' => __('You have to confirm.', true)));
	echo $form->submit(__('Continue', true));
	echo $form->end();
?>