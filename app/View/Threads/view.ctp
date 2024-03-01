<div class="users index">
    <h2><?= __('Conversation with: %s', h($participant)); ?></h2>
    <div class="c-container">
        <div class="action-button" >
            <?= $this->Form->create('Message', [
                'url' => [
                    'controller' => 'messages',
                    'action' => 'add'
                ]
            ]); ?>
            <?= $this->Form->input('content', [
                'type' => 'textarea',
                'label' => ''
            ]); ?>
            <?= $this->Form->input('threadId', [
                'hidden' => true,
                'label' => '',
                'default' => $threadId
            ]); ?>
            <?= $this->Form->end(__('Reply Message')); ?>

        </div>
    </div>

	<div class="c-container inbox">
        <?php $threadController = new ThreadsController(); ?>
        <?php $currentUser = (AuthComponent::user('id')); ?>
        <?php foreach ($messages as $message): ?>
            <?php
                $isSender = ($currentUser != $message['Message']['user_id']);
                $photo = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp';
                if($message['User']['photo']) {
                    $photo = '/uploads/' . $message['User']['photo'];
                }
            ?>
            <div class="message-block conversation <?= ($isSender) ? 'c-left' : 'c-right'?>">
                <?= $this->Html->image($photo, [
                    'class' => 'avatar',
                    'alt' => 'Your Image'
                ]); ?>
                <div class="message-content <?= ($isSender) ? 'left-content' : 'right-content'?>">
                    <div class="body">
                        <?php echo $message['Message']['content'] ?>
                    </div>
                    <div class="footer">
                        <?php echo $threadController->dateToString($message['Message']['created'], true); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php unset($thread); ?>
	</div>
</div>

<?= $this->Element('actions') ?>