<?php

namespace ThunderAdmin;

$unread = count(array_filter($admin_notifications, static fn (object $item): bool => !$item->read));
?>
<div class="ta-dropdown" data-ta-dropdown>
    <button class="ta-icon-button" type="button" data-ta-dropdown-toggle aria-label="Notifications" aria-expanded="false">
        <i class="fa-solid fa-bell" aria-hidden="true"></i>
        <?php if ($unread > 0): ?>
            <span class="ta-icon-button__badge"><?= e($unread > 99 ? '99+' : $unread) ?></span>
        <?php endif; ?>
    </button>

    <div class="ta-dropdown__panel ta-notifications" data-ta-dropdown-panel hidden>
        <div class="ta-dropdown__header">
            <div class="ta-dropdown__heading-wrap">
                <h2 class="ta-dropdown__heading">Notifications</h2>
                <span class="ta-dropdown__count"><?= e($unread) ?> unread</span>
            </div>
            <?php do_action('admin_notifications_header'); ?>
        </div>

        <div class="ta-notifications__list">
            <?php if ($admin_notifications === []): ?>
                <div class="ta-notifications__empty">
                    <i class="ta-notifications__empty-icon fa-regular fa-bell" aria-hidden="true"></i>
                    <span class="ta-notifications__empty-text">You have no notifications.</span>
                </div>
            <?php else: ?>
                <?php foreach ($admin_notifications as $notification): ?>
                    <a class="ta-notification<?= $notification->read ? '' : ' ta-notification--unread' ?>" href="<?= e($notification->link) ?>">
                        <span class="ta-notification__icon ta-notification__icon--<?= e($notification->type) ?>">
                            <i class="<?= e($notification->icon) ?>" aria-hidden="true"></i>
                        </span>
                        <span class="ta-notification__body">
                            <strong class="ta-notification__title"><?= e($notification->title) ?></strong>
                            <?php if ($notification->message !== ''): ?>
                                <span class="ta-notification__message"><?= e($notification->message) ?></span>
                            <?php endif; ?>
                            <?php if ($notification->created_at !== ''): ?>
                                <span class="ta-notification__time"><?= e(notification_time($notification->created_at)) ?></span>
                            <?php endif; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="ta-dropdown__footer">
            <?php do_action('admin_notifications_footer'); ?>
        </div>
    </div>
</div>
