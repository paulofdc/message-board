<div class="users form">
    <h2><?= __('User Profile'); ?></h2>
	<div>
		<div class="horizontal-alignment">
			<?= 
                $this->Element('preview_photo', [
                    'profile_photo' => $user['photo']
                ]) 
            ?>
            <div>
                <h3><?= $user['name']; ?>, <?= $user['age']; ?></h3>
                <h5><?= __('Gender: %s', ucwords($user['gender'])); ?></h5>
                <h5><?= __('Birthdate: %s', $user['birthdate']); ?></h5>
                <h5><?= __('Joined: %s', $user['date_joined']); ?></h5>
                <h5><?= __('Last Login: %s', $user['last_login']); ?></h5>
                <?php if(AuthComponent::user('id') == $user['id']) : ?>
                    <h5 class="mt-20-i">
                        <?= $this->Html->link(
                            __('Edit Profile'), [
                                'controller' => "users", 
                                'action' => 'edit', 
                                $user['id']
                            ]); 
                        ?>
                    </h5>
                    <h5>
                        <?= $this->Html->link(
                            __('Change Email Address'), [
                                'controller' => "users", 
                                'action' => 'changeEmailAddress', 
                                $user['id']
                            ]); 
                        ?>
                    </h5>
                    <h5>
                        <?= $this->Html->link(
                            __('Change Password'), [
                                'controller' => "users", 
                                'action' => 'changePassword', 
                                $user['id']
                            ]); 
                        ?>
                    </h5>
                <?php endif; ?>
            </div>
		</div>
        <div class="mt-10-i">
            <h4><?= __('Hubby:'); ?></h4>
            <p class="max-w-profile"><?= $user['other_details']; ?></p>
        </div>
	</div>
</div>

<?= $this->Element('actions') ?>

<?= $this->Element('preview_photo_script') ?>