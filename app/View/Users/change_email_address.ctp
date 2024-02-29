
<div class="users form">
    <h2><?= __('Change Email Address'); ?></h2>
    <?= $this->Element('error_messages') ?>
	<div>
        <?php
            echo $this->Form->create('User', [
                'type' => 'file',
                'inputDefaults' => [
                    'error' => false
                ]
            ]); 
            echo $this->Form->input('current_email_address', [
                'type' => 'email'
            ]);
            echo $this->Form->input('new_email_address', [
                'type' => 'email'
            ]);
            echo $this->Form->input('confirm_new_email_address', [
                'type' => 'email',
                'label' => 'Re-type Email Address'
            ]);
            echo $this->Form->end(__('Submit'));
        ?>
	</div>
</div>

<?= $this->Element('actions') ?>