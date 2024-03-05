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
        <div class="c-container">
            <input id="search" type="text" placeholder="<?= __('Who are you looking for?') ?>" data-type="thread"><br><br>
        </div>
        <div class="inbox-messages">
            <?php foreach ($threads as $thread): ?>
                <?php
                    $dataId = $thread['Thread']['id'];
                    $isReceiver = AuthComponent::user('id') == $thread['Thread']['receiver_id'];
                    $name = ($isReceiver) ? $thread['Owner']['name'] : $thread['Receiver']['name'];
                    $image = ($isReceiver) ? $thread['Owner']['photo'] : $thread['Receiver']['photo'];
                    $photo = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp';
                    if($image) {
                        $photo = $image;
                    }
                ?>
                <a class="thread-link t-link-<?= $dataId ?>" href="<?php echo $this->Html->url(['action' => 'view', $thread['Thread']['id']]); ?>" data-id="<?= $dataId ?>">
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
                        <div class="delete-container-thread delete-thread-container-<?= $dataId ?>">
                            <span class="delete-message-btn-thread fa fa-trash-o" data-id="<?= $dataId ?>"></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php unset($thread); ?>
        <div id="search-container">
            <span class="empty-m">No data available</span>
            <div class="inbox-search"></div>
        </div>

        <?php if($threadCount > $maxLimit ) :?>
            <div id="load-btn-container" class="c-container">
                <button id="load-more-btn" data-type="thread">
                    <?= __('Show more') ?>
                </button>
            </div>
        <?php endif; ?>
	</div>
</div>

<?= $this->Element('actions') ?>
<?= $this->Element('custom-scripts') ?>