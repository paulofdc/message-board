<div class="custom-error-container">
    <?php if (!empty($this->validationErrors['User'])): ?>
        <div class="validation-errors">
            <ul class="error-list">
                <?php foreach ($this->validationErrors['User'] as $field => $errors): ?>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>