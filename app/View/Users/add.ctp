<div class="users form">
	<fieldset>
		<legend><?php echo __('Register'); ?></legend>
		<?= 
			$this->Element('error_messages', [
				'type' => 'User'
			])  
		?>
		<?php 			
			echo $this->Form->create('User', [
				'type' => 'file',
				'inputDefaults' => [
					'error' => false
				]
			]); 
		?>
		<div class="horizontal-alignment">
			<?= $this->Element('preview_photo') ?>
			<button type="button" id="upload-btn"><?= __('Upload Picture'); ?></button>
			<?= $this->Form->input('photo', [
				'label' => '',
				'id' => 'profile-image-upload',
				'type' => 'file',
				'hidden' => true
			]); ?>
		</div>
		<?php
			echo $this->Form->input('name');
			echo $this->Form->input('email');
			echo $this->Form->input('password');
			echo $this->Form->input('confirm_password', [
				'type' => 'password',
				'required' => true
			]);
			echo $this->Form->input('gender', [
				'type' => 'radio',
				'style' => 'margin-left: 20px',
				'options' => [
					'male' => 'Male',
					'female' => 'Female'
				],
				'fieldset' => ['class' => 'horizontal-alignment']
			]);
		?>
		<?php
			echo $this->Form->input('birthdate', [
				'type' => 'text',
				'autocomplete' => 'off',
				'readonly' => true,
				'id' => 'birthdate',
			]);
			echo $this->Form->input('other_details', [
				'type' => 'textarea'
			]);
		?>
		<?php echo $this->Form->end(__('Submit')); ?>
	</fieldset>
</div>

<?= $this->Element('actions') ?>

<?= $this->Element('preview_photo_script') ?>
<?= $this->Element('birthdate_calendar_script') ?>
<?= $this->Element('custom-scripts') ?>