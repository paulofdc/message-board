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
            <?= $this->Form->end([
                'id' => 'reply-btn',
                'label' => 'Reply Message'
            ]); ?>

        </div>
    </div>

	<div class="c-container inbox">
        <?php $threadController = new ThreadsController(); ?>
        <?php $currentUser = (AuthComponent::user('id')); ?>
        <?php foreach ($messages as $message): ?>
            <?php
                $dataId = $message['Message']['id'];
                $isSender = ($currentUser != $message['Message']['user_id']);
                $photo = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp';
                if($message['User']['photo']) {
                    $photo = '/uploads/' . $message['User']['photo'];
                }
            ?>
            <div class="message-block m-block-<?= $dataId ?> conversation <?= ($isSender) ? 'c-left' : 'c-right'?>" data-id="<?= $dataId ?>">
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
                <?php if(!$isSender) :?>
                    <div class="delete-container delete-container-<?= $dataId ?>">
                        <span class="delete-message-btn" data-id="<?= $dataId ?>">Delete</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php unset($thread); ?>
	</div>
</div>

<?= $this->Element('actions') ?>
<?= $this->Element('custom-scripts') ?>