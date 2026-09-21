<?php

namespace ThunderAdmin;
?>
<?php if ($admin_breadcrumbs !== []): ?>
    <nav class="ta-breadcrumbs" aria-label="Breadcrumb">
        <?php foreach ($admin_breadcrumbs as $index => $crumb): ?>
            <?php $isLast = $index === array_key_last($admin_breadcrumbs); ?>
            <span class="ta-breadcrumbs__item">
                <?php if (!$isLast): ?>
                    <a class="ta-breadcrumbs__link" href="<?= e($crumb->link ?? '#') ?>"><?= e($crumb->title ?? '') ?></a>
                    <i class="ta-breadcrumbs__separator fa-solid fa-chevron-right" aria-hidden="true"></i>
                <?php else: ?>
                    <span class="ta-breadcrumbs__current" aria-current="page"><?= e($crumb->title ?? '') ?></span>
                <?php endif; ?>
            </span>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
