@php
    $popupMessages = [];

    $popupErrors = $errors ?? null;
    if ($popupErrors && method_exists($popupErrors, 'any') && $popupErrors->any()) {
        foreach ($popupErrors->all() as $err) {
            $popupMessages[] = ['type' => 'error', 'text' => $err];
        }
    }

    foreach (['error' => 'error', 'danger' => 'error', 'success' => 'success', 'warning' => 'warning', 'info' => 'info', 'status' => 'info'] as $key => $type) {
        if (session()->has($key)) {
            $value = session($key);
            if (is_string($value) && trim($value) !== '') {
                $popupMessages[] = ['type' => $type, 'text' => $value];
            }
        }
    }
@endphp

@if (! empty($popupMessages))
<div class="rj-popup-stack" id="rj-popup-stack" role="region" aria-live="polite">
    @foreach ($popupMessages as $i => $msg)
        <div class="rj-popup rj-popup--{{ $msg['type'] }}" data-popup style="animation-delay: {{ $i * 80 }}ms;">
            <div class="rj-popup__icon" aria-hidden="true">
                @switch($msg['type'])
                    @case('success')
                        <svg viewBox="0 0 24 24" fill="none" width="20" height="20"><path d="M5 12.5l4.5 4.5L19 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @break
                    @case('warning')
                        <svg viewBox="0 0 24 24" fill="none" width="20" height="20"><path d="M12 4l10 17H2L12 4z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/><path d="M12 10v5M12 18v.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
                        @break
                    @case('info')
                        <svg viewBox="0 0 24 24" fill="none" width="20" height="20"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2.2"/><path d="M12 11v5M12 8v.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
                        @break
                    @default
                        <svg viewBox="0 0 24 24" fill="none" width="20" height="20"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2.2"/><path d="M12 7v6M12 16v.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
                @endswitch
            </div>
            <div class="rj-popup__body">
                <strong class="rj-popup__title">
                    @switch($msg['type'])
                        @case('success') Tudo certo @break
                        @case('warning') Atenção @break
                        @case('info') Aviso @break
                        @default Não foi possível continuar
                    @endswitch
                </strong>
                <p class="rj-popup__text">{{ $msg['text'] }}</p>
            </div>
            <button type="button" class="rj-popup__close" aria-label="Fechar" data-popup-close>
                <svg viewBox="0 0 24 24" fill="none" width="16" height="16"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
            </button>
            <span class="rj-popup__progress"></span>
        </div>
    @endforeach
</div>

<style>
    .rj-popup-stack {
        position: fixed;
        top: 20px;
        right: 20px;
        left: auto;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: calc(100vw - 40px);
        width: 380px;
        pointer-events: none;
    }

    .rj-popup {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 40px 14px 14px;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 10px 32px rgba(15, 15, 15, 0.12), 0 2px 6px rgba(15, 15, 15, 0.06);
        border: 1px solid rgba(15, 15, 15, 0.06);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        overflow: hidden;
        opacity: 0;
        transform: translateX(20px);
        animation: rj-popup-in 0.32s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        pointer-events: auto;
    }

    .rj-popup.rj-popup--leaving {
        animation: rj-popup-out 0.22s ease-in forwards;
    }

    @keyframes rj-popup-in {
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes rj-popup-out {
        to { opacity: 0; transform: translateX(20px); }
    }

    .rj-popup__icon {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .rj-popup--error .rj-popup__icon { background: #fef2f2; color: #dc2626; }
    .rj-popup--success .rj-popup__icon { background: #f0fdf4; color: #16a34a; }
    .rj-popup--warning .rj-popup__icon { background: #fffbeb; color: #d97706; }
    .rj-popup--info .rj-popup__icon { background: #eff6ff; color: #2563eb; }

    .rj-popup__body { flex: 1; min-width: 0; }

    .rj-popup__title {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #1B1B1B;
        margin-bottom: 2px;
        letter-spacing: -0.1px;
    }

    .rj-popup__text {
        margin: 0;
        font-size: 13px;
        line-height: 1.45;
        color: #4a4745;
        word-wrap: break-word;
    }

    .rj-popup__close {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 22px;
        height: 22px;
        border-radius: 6px;
        border: none;
        background: transparent;
        color: #8a8785;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, color 0.15s;
    }

    .rj-popup__close:hover { background: #f2efec; color: #1B1B1B; }

    .rj-popup__progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
        transform-origin: left;
        animation: rj-popup-progress 6s linear forwards;
    }

    .rj-popup--error .rj-popup__progress { background: #dc2626; }
    .rj-popup--success .rj-popup__progress { background: #16a34a; }
    .rj-popup--warning .rj-popup__progress { background: #d97706; }
    .rj-popup--info .rj-popup__progress { background: #2563eb; }

    @keyframes rj-popup-progress {
        from { transform: scaleX(1); }
        to { transform: scaleX(0); }
    }

    @media (max-width: 480px) {
        .rj-popup-stack {
            top: 12px;
            right: 12px;
            left: 12px;
            width: auto;
            max-width: none;
        }
    }
</style>

<script>
    (function () {
        const stack = document.getElementById('rj-popup-stack');
        if (!stack) return;

        const dismiss = (popup) => {
            if (!popup || popup.dataset.dismissed) return;
            popup.dataset.dismissed = '1';
            popup.classList.add('rj-popup--leaving');
            popup.addEventListener('animationend', () => {
                popup.remove();
                if (!stack.querySelector('[data-popup]')) stack.remove();
            }, { once: true });
        };

        stack.querySelectorAll('[data-popup-close]').forEach((btn) => {
            btn.addEventListener('click', () => dismiss(btn.closest('[data-popup]')));
        });

        stack.querySelectorAll('[data-popup]').forEach((popup, idx) => {
            const delay = 6000 + idx * 80;
            setTimeout(() => dismiss(popup), delay);
        });
    })();
</script>
@endif
