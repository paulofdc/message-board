<?php
    $photo = null;
    if(AuthComponent::user() && AuthComponent::user('photo') != null) {
        $photo = '/uploads/' . AuthComponent::user('photo');
    } else {
        $photo = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp';
    }
?>

<?= $this->Html->image($photo, [
    'id' => 'imagePreview',
    'style' => 'max-width: 200px; max-height: 200px;',
    'alt' => 'Your Image'
]); ?>