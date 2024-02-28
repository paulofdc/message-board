<div class="index">
	<h2><?php echo __('Greetings'); ?></h2>
    <p>Thank you for registering!</p>
    <ul class="actions" style="margin: 0">
        <li>
            <?= 
                $this->Html->link(
                    __('Back to Homepage'),
                    array('controller' => "home", 'action' => 'index'),
                    ['class' => 'actions']
                ); 
            ?>
        </li>
    </ul>
</div>

<?= $this->Element('actions') ?>