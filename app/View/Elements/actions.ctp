<?php $currentUser = AuthComponent::user(); ?>
<div class="actions">
	<?php if($currentUser) : ?>
		<h3><?php echo __('Hi %s!', h(AuthComponent::user('name'))); ?></h3>

		<ul>
			<li><?php echo $this->Html->link(__('My account'), array('controller' => "users", 'action' => 'profile', AuthComponent::user('id'))); ?></li>
		</ul>
	<?php endif; ?>
	<ul>
		<li><?php echo $this->Html->link(__('Home'), array('controller' => "home", 'action' => 'index')); ?></li>
	</ul>
	<?php if(!$currentUser) : ?>
	<ul>
		<li><?php echo $this->Html->link(__('Login'), array('controller' => "users", 'action' => 'login')); ?></li>
	</ul>
	<ul>
		<li><?php echo $this->Html->link(__('Register'), array('controller' => "users", 'action' => 'add')); ?></li>
	</ul>
	<?php endif; ?>
	<ul>
		<li><?php echo $this->Html->link(__('Users'), array('controller' => "users", 'action' => 'index')); ?></li>
	</ul>
	<?php if($currentUser) : ?>
		<ul>
			<li>
				<?= 
					$this->Form->postLink(
						__('Logout'),
						array('controller' => "users", 'action' => 'logout'),
						array('confirm' => 'Are you sure you want to logout?')
					);
				?>
			</li>
		</ul>
	<?php endif; ?>
</div>