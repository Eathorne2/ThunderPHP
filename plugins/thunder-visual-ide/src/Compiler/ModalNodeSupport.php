<?php

declare(strict_types=1);

namespace ThunderVisualIde\Compiler;

final class ModalNodeSupport
{
    /** @param array<string,string> $attributes */
    public static function addAttributesToFirstElement(string $html, array $attributes): string
    {
        $length = strlen($html);
        $start = null;

        for ($i = 0; $i < $length - 1; $i++) {
            if ($html[$i] !== '<') {
                continue;
            }
            $next = $html[$i + 1] ?? '';
            if (preg_match('/[A-Za-z]/', $next) === 1) {
                $start = $i;
                break;
            }
        }

        if ($start === null) {
            return '';
        }

        $quote = null;
        $end = null;
        for ($i = $start + 1; $i < $length; $i++) {
            $character = $html[$i];
            if ($quote !== null) {
                if ($character === $quote && ($i === 0 || $html[$i - 1] !== '\\')) {
                    $quote = null;
                }
                continue;
            }
            if ($character === '"' || $character === "'") {
                $quote = $character;
                continue;
            }
            if ($character === '>') {
                $end = $i;
                break;
            }
        }

        if ($end === null) {
            return '';
        }

        $before = substr($html, 0, $start);
        $tag = substr($html, $start, $end - $start + 1);
        $after = substr($html, $end + 1);
        foreach (array_keys($attributes) as $attribute) {
            $quoted = preg_quote((string) $attribute, '/');
            $tag = preg_replace(
                '/\s+' . $quoted . '\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i',
                '',
                $tag
            ) ?? $tag;
        }

        $insertAt = str_ends_with($tag, '/>') ? strlen($tag) - 2 : strlen($tag) - 1;
        $tag = substr($tag, 0, $insertAt)
            . ViewNodeSupport::attributes($attributes)
            . substr($tag, $insertAt);

        return $before . $tag . $after;
    }

    public static function runtimeScript(): string
    {
        return <<<'HTML'
<script>
(function () {
    if (window.ThunderModal && window.ThunderModal.__tviRuntime === true) {
        if (typeof window.ThunderModal.refresh === 'function') {
            window.ThunderModal.refresh();
        }
        return;
    }

    const previousFocus = new WeakMap();
    const focusableSelector = '[autofocus], button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

    const modalById = function (id) {
        if (!id) return null;
        return document.getElementById(String(id));
    };

    const openModals = function () {
        return Array.from(document.querySelectorAll('[data-tvi-modal][aria-hidden="false"]'));
    };

    const topOpenModal = function () {
        return openModals().sort(function (a, b) {
            return Number(getComputedStyle(a).zIndex || 0) - Number(getComputedStyle(b).zIndex || 0);
        }).pop() || null;
    };

    const updateTriggerState = function (id, isOpen) {
        document.querySelectorAll('[data-tvi-modal-target="' + String(id) + '"]').forEach(function (trigger) {
            trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    };

    const updateBodyScroll = function () {
        const lockedOpenModal = document.querySelector(
            '[data-tvi-modal][aria-hidden="false"][data-tvi-lock-scroll="1"]'
        );
        document.documentElement.classList.toggle('thv-modal-open', Boolean(lockedOpenModal));
        document.body.classList.toggle('thv-modal-open', Boolean(lockedOpenModal));
    };

    const focusModal = function (modal) {
        const target = modal.querySelector(focusableSelector);
        (target || modal).focus({ preventScroll: true });
    };

    const open = function (id) {
        const modal = modalById(id);
        if (!modal) return false;
        previousFocus.set(modal, document.activeElement);
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        modal.classList.add('is-open');
        updateTriggerState(id, true);
        updateBodyScroll();
        window.requestAnimationFrame(function () { focusModal(modal); });
        modal.dispatchEvent(new CustomEvent('tvi:modal-open', { bubbles: true }));
        return true;
    };

    const close = function (id) {
        const modal = modalById(id);
        if (!modal) return false;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
        updateTriggerState(id, false);
        updateBodyScroll();
        const target = previousFocus.get(modal);
        if (target && typeof target.focus === 'function' && document.contains(target)) {
            target.focus({ preventScroll: true });
        }
        modal.dispatchEvent(new CustomEvent('tvi:modal-close', { bubbles: true }));
        return true;
    };

    const toggle = function (id) {
        const modal = modalById(id);
        if (!modal) return false;
        return modal.getAttribute('aria-hidden') === 'false' ? close(id) : open(id);
    };

    const refresh = function () {
        document.querySelectorAll('[data-tvi-modal]').forEach(function (modal) {
            updateTriggerState(modal.id, modal.getAttribute('aria-hidden') === 'false');
        });
        updateBodyScroll();
    };

    const api = {
        open: open,
        close: close,
        toggle: toggle,
        refresh: refresh,
        __tviRuntime: true
    };
    window.ThunderModal = api;

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-tvi-modal-target]');
        if (trigger) {
            const id = trigger.getAttribute('data-tvi-modal-target') || '';
            const action = trigger.getAttribute('data-tvi-modal-action') || 'toggle';
            if (typeof api[action] === 'function') {
                event.preventDefault();
                api[action](id);
            }
            return;
        }

        const modal = event.target.closest('[data-tvi-modal]');
        if (
            modal
            && event.target === modal
            && modal.getAttribute('data-tvi-close-backdrop') === '1'
        ) {
            api.close(modal.id);
        }
    });

    document.addEventListener('keydown', function (event) {
        const modal = topOpenModal();
        if (!modal) return;

        if (event.key === 'Escape' && modal.getAttribute('data-tvi-close-escape') === '1') {
            event.preventDefault();
            api.close(modal.id);
            return;
        }

        if (event.key !== 'Tab') return;
        const focusable = Array.from(modal.querySelectorAll(focusableSelector)).filter(function (element) {
            return element.offsetParent !== null || element === document.activeElement;
        });
        if (!focusable.length) {
            event.preventDefault();
            modal.focus({ preventScroll: true });
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    refresh();
})();
</script>
HTML;
    }
}
