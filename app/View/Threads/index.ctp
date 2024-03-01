<div class="users index">
    <h2><?= __('Message List'); ?></h2>
    <div class="c-container">
        <div style="position: absolute; right: 0; margin-right: 10px">
            <?= 
                $this->Html->link(
                    __('New Message'),
                    array('controller' => "threads", 'action' => 'add'),
                    ['class' => 'new-message-btn']
                ); 
            ?>
        </div>
    </div>
	<div>
        <?php foreach ($threads as $thread): ?>

        <?php endforeach; ?>
        <?php unset($thread); ?>
	</div>
</div>

<?= $this->Element('actions') ?>