<?php
/**
 * Messages affichés une seule fois (ex. "Vous avez bien été déconnecté.").
 *
 * @var App\Core\View $this
 * @var array<int, array{type: string, message: string}> $flashes
 */
?>
<?php if (count($flashes) > 0) : ?>
<div class="flashes container">
<?php foreach ($flashes as $flash) : ?>
    <p class="alert alert--<?= $this->escape($flash['type']) ?>" role="status"><?= $this->escape($flash['message']) ?></p>
<?php endforeach; ?>
</div>
<?php endif; ?>
