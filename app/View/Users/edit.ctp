<div class="users form">
	<fieldset>
		<legend><?php echo __('Update Profile'); ?></legend>
		<?= 
			$this->Element('error_messages', [
				'type' => 'User'
			])  
		?>
		<?= 
			$this->Form->create('User', [
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
			echo $this->Form->input('id');
			echo $this->Form->input('name');

			/**
			 * TO DO
			 */
			// echo $this->Form->input('email');

			echo $this->Form->input('birthdate', [
				'type' => 'text',
				'autocomplete' => 'off',
				'readonly' => true,
				'id' => 'birthdate',
			]);
		?>
		<?php
			echo $this->Form->input('gender', [
				'type' => 'radio',
				'default' => AuthComponent::user('gender'),
				'style' => 'margin-left: 20px',
				'options' => [
					'male' => 'Male',
					'female' => 'Female'
				],
				'fieldset' => ['class' => 'horizontal-alignment']
			]);
		?>
		<?php
			echo $this->Form->input('other_details', [
				'type' => 'textarea'
			]);
		?>
	</fieldset>
<?php echo $this->Form->end(__('Update')); ?>
</div>

<?= $this->Element('actions') ?>
<?= $this->Element('preview_photo_script') ?>
<?= $this->Element('birthdate_calendar_script') ?>
<?= $this->Element('custom-scripts') ?>