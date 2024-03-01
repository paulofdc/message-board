<div class="users form">
    <fieldset>
        <legend><?= __('Create New Message'); ?></legend>
        <?=                 
            $this->Element('error_messages', [
                'type' => 'Message'
            ])  
        ?>
        <?= $this->Form->create('Thread'); ?>
        <?= $this->Form->input('receivers', [
            'class' => 'select2-recipient'
        ]); ?>
        <?= $this->Form->input('message', [
            'type' => 'textarea'
        ]); ?>
        <?= $this->Form->end(__('Create')); ?>
    </fieldset>
</div>

<?= $this->Element('actions') ?>
<?= $this->Element('custom-scripts') ?>