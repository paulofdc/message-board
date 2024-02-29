<style>
	.error-list {
		padding: 0;
		margin: 0;
	}
	.error-list li {
		color: red;
		margin-bottom: 5px;
	}
</style>

<div class="users form">
	<fieldset>
		<legend><?php echo __('Register'); ?></legend>
		<div class="custom-error-container">
			<?php if (!empty($this->validationErrors['User'])): ?>
				<div class="validation-errors">
					<ul class="error-list">
						<?php foreach ($this->validationErrors['User'] as $field => $errors): ?>
							<?php foreach ($errors as $error): ?>
								<li><?= $error ?></li>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
		<?php
			echo $this->Form->create('User', [
				'type' => 'file',
				'inputDefaults' => [
					'error' => false
				]
			]); 
			echo $this->Form->input('name');
			echo $this->Form->input('email');
			echo $this->Form->input('password');
			echo $this->Form->input('confirm_password', [
				'type' => 'password',
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
		<?= $this->Html->image('https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp', [
			'id' => 'imagePreview',
			'style' => 'max-width: 200px; max-height: 200px;',
			'alt' => 'Your Image'
		]); ?>
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

<?= $this->Element('actions') ?>

<script>
	$(document).ready(function() {
		$('#profile-image-upload').change(function() {
			var file = this.files[0];
			if (file) {
				var reader = new FileReader();
				reader.onload = function(e) {
					$('#imagePreview').attr('src', e.target.result).show();
				}
				reader.readAsDataURL(file);
			}
		});
	});
</script>