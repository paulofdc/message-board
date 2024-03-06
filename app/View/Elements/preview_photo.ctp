<?php
    $photo = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp';;
    $profile_photo = $profile_photo ?? $photo;
    if($profile_photo) {
        //For profile viewing
        $photo = $profile_photo;
    } else {
        if(AuthComponent::user() && AuthComponent::user('photo') != null) {
            $photo = AuthComponent::user('photo');
        }
    }
?>

<?= $this->Html->image($photo, [
    'id' => 'imagePreview',
    'style' => 'max-width: 200px; max-height: 200px;',
    'alt' => 'Your Image'
]); ?>