<div class="actions">
	<?php if(AuthComponent::user()) : ?>
		<h3><?php echo __('Hi %s!', h(AuthComponent::user('name'))); ?></h3>
	<?php endif; ?>
	<ul>
		<li><?php echo $this->Html->link(__('Home'), array('controller' => "home", 'action' => 'index')); ?></li>
	</ul>
	<?php if(!AuthComponent::user()) : ?>
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
	<?php if(AuthComponent::user()) : ?>
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