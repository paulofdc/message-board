<div class="users form">
    <h2><?= __('Change Password'); ?></h2>
    <?= $this->Element('error_messages') ?>
	<div>
        <?php
            echo $this->Form->create('User', [
                'type' => 'file',
                'inputDefaults' => [
                    'error' => false
                ]
            ]); 
            echo $this->Form->input('current_password', [
                'type' => 'password'
            ]);
            echo $this->Form->input('password', [
                'type' => 'password',
                'label' => 'New Password'
            ]);
            echo $this->Form->input('confirm_password', [
                'type' => 'password',
                'label' => 'Confirm New Password'
            ]);
            echo $this->Form->end(__('Submit'));
        ?>
	</div>
</div>

<?= $this->Element('actions') ?>