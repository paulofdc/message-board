<div class="users index">
    <h2><?= __('Conversation with: %s', h($participant)); ?></h2>
    <div class="c-container">
        <div>
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
    </div>

	<div class="c-container inbox">
        <div class="c-container">
            <input id="search" type="text" placeholder="Search message..." data-type="message"><br><br>
        </div>
        <?php $threadController = new ThreadsController(); ?>
        <?php $currentUser = (AuthComponent::user('id')); ?>
        <div class="inbox-messages">
            <?php foreach ($messages as $message): ?>
                <?php
                    $dataId = $message['Message']['id'];
                    $messageOwner = $message['Message']['user_id'] ?? '';
                    $isSender = ($currentUser != $messageOwner);
                    $photo = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp';
                    $content = $message['Message']['content'];
                    if($message['User']['photo']) {
                        $photo = $message['User']['photo'];
                    }
                ?>
                <div class="message-block m-block-<?= $dataId ?> conversation <?= ($isSender) ? 'c-left' : 'c-right'?>" data-id="<?= $dataId ?>">
                    <a href="<?= $this->Html->url(['controller' => 'users', 'action' => 'profile', $messageOwner]) ?>" target="_blank">
                        <?= $this->Html->image($photo, [
                            'class' => 'avatar',
                            'alt' => 'Your Image'
                        ]); ?>
                    </a>
                    <div class="message-content <?= ($isSender) ? 'left-content' : 'right-content'?>" data-id="<?= $dataId ?>">
                        <div class="body <?php echo ($threadController->checkTextLength($content)) ? 'ellipsis' : '' ?>">
                            <?php echo $content ?>
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
        </div>
        <?php unset($thread); ?>

        <div id="search-container">
            <span class="empty-m">No data available</span>
            <div class="inbox-search"></div>
        </div>

        <?php if($messageCount > $maxLimit ) :?>
            <input type="hidden" id="count" value="<?= $messageCount ?>">
            <div id="load-btn-container" class="c-container">
                <button id="load-more-btn" data-type="message">
                    <?= __('Show more') ?>
                </button>
            </div>
        <?php endif; ?>
	</div>

</div>

<?= $this->Element('actions') ?>
<?= $this->Element('custom-scripts') ?>