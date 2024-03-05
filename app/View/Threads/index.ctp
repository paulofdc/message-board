<div class="users index">
    <h2><?= __('Message List'); ?></h2>
    <div class="c-container">
        <div class="action-button" >
            <?= 
                $this->Html->link(
                    __('New Message'),
                    array('controller' => "threads", 'action' => 'add'),
                    ['class' => 'new-message-btn']
                ); 
            ?>
        </div>
    </div>

	<div class="c-container inbox">
        <?php $threadController = new ThreadsController(); ?>
        <div class="inbox-messages">
            <?php foreach ($threads as $thread): ?>
                <?php
                    $dataId = $thread['Thread']['id'];
                    $isReceiver = AuthComponent::user('id') == $thread['Thread']['receiver_id'];
                    $name = ($isReceiver) ? $thread['Owner']['name'] : $thread['Receiver']['name'];
                    $image = ($isReceiver) ? $thread['Owner']['photo'] : $thread['Receiver']['photo'];
                    if($image) {
                        $photo = $image;
                    } else {
                        $photo = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp';
                    }
                ?>
                <a class="thread-link" href="<?php echo $this->Html->url(['action' => 'view', $thread['Thread']['id']]); ?>" data-id="<?= $dataId ?>">
                    <div class="message-block">
                        <?= $this->Html->image($photo, [
                            'class' => 'avatar',
                            'alt' => 'Your Image'
                        ]); ?>
                        <div class="message-content">
                            <div class="header">
                                <?php echo $name ?>
                            </div>
                            <div class="body">
                                <?php echo $thread['Message'][0]['content'] ?? "" ?>
                            </div>
                            <div class="footer">
                                <?php echo $threadController->dateToString($thread['Message'][0]['created'] ?? "", true); ?>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php unset($thread); ?>

        <?php if($threadCount > $maxLimit ) :?>
            <div id="load-btn-container" class="c-container">
                <button id="load-more-btn" data-type="thread">
                    <?= __('Load more') ?>
                </button>
            </div>
        <?php endif; ?>
	</div>
</div>

<?= $this->Element('actions') ?>
<?= $this->Element('custom-scripts') ?>