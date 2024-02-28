<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Register'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->input('password');
		echo $this->Form->input('confirm_password', [
			'required' => true
		]);
		echo $this->Form->input('gender', [
			'type' => 'select',
			'options' => [
				'male' => 'Male',
				'female' => 'Female'
			]
		]);
	?>
	<img id="blah" src="#" alt="your image" />
	<?php
		echo $this->Form->input('photo', [
			'id' => 'profile-image-upload',
			'type' => 'file'
		]);
		echo $this->Form->input('birthdate');
		echo $this->Form->input('other_details', [
			'required' => false
		]);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
	</ul>
</div>
