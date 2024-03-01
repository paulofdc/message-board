<div class="custom-error-container">
    <?php if (!empty($this->validationErrors[$type])): ?>
        <div class="validation-errors">
            <ul class="error-list">
                <?php foreach ($this->validationErrors[$type] as $field => $errors): ?>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>