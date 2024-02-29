<style>
	.horizontal-alignment {
		display: flex;
		align-items: center;
	}

	.horizontal-alignment > * {
		margin-right: 10px;
	}
</style>
<div class="users form">
	<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Update Profile'); ?></legend>
		<div class="horizontal-alignment">
			<?= $this->Element('preview_photo') ?>
			<?= $this->Form->input('photo', [
				'id' => 'profile-image-upload',
				'type' => 'file'
			]); ?>
		</div>
		<?php
			echo $this->Form->input('name');

			/**
			 * TO DO
			 */
			// echo $this->Form->input('email');
			echo $this->Form->input('birthdate');
			echo $this->Form->input('gender', [
				'type' => 'select',
				'default' => AuthComponent::user('gender'),
				'options' => [
					'male' => 'Male',
					'female' => 'Female'
				]
			]);
			echo $this->Form->input('other_details');
		?>
	</fieldset>
<?php echo $this->Form->end(__('Update')); ?>
</div>

<?= $this->Element('actions') ?>
<?= $this->Element('preview_photo_script') ?>