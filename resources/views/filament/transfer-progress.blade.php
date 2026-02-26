<style>
    .fi-sc-wizard-footer { display: none !important; }

    .transfer-progress-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 1rem;
        gap: 2rem;
    }

    /* ── loading ── */
    .transfer-progress-text-center {
        text-align: center;
    }

    .transfer-progress-text-center h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 0.25rem 0;
    }

    :is(.dark) .transfer-progress-text-center h3 {
        color: #fff;
    }

    .transfer-progress-text-center p {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin: 0;
    }

    .transfer-progress-bar-container {
        width: 100%;
        max-width: 28rem;
    }

    .transfer-progress-bar-header {
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .transfer-progress-bar-label {
        color: var(--gray-600);
    }

    :is(.dark) .transfer-progress-bar-label {
        color: var(--gray-400);
    }

    .transfer-progress-bar-value {
        color: var(--primary-600);
        font-weight: 700;
    }

    :is(.dark) .transfer-progress-bar-value {
        color: var(--primary-400);
    }

    .transfer-progress-bar-track {
        width: 100%;
        background: var(--gray-200);
        border-radius: 9999px;
        height: 1rem;
        overflow: hidden;
        position: relative;
    }

    :is(.dark) .transfer-progress-bar-track {
        background: var(--gray-700);
    }

    .transfer-progress-bar-fill {
        height: 1rem;
        border-radius: 9999px;
        background: var(--primary-500);
        transition: width 0.6s ease-out;
        position: relative;
        overflow: hidden;
    }

    .transfer-progress-bar-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.3),
            transparent
        );
        animation: transfer-progress-shimmer 1.5s infinite;
    }

    @keyframes transfer-progress-shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .transfer-progress-days {
        font-size: 0.8rem;
        color: var(--gray-400);
        text-align: center;
        margin-top: 0.5rem;
    }

    :is(.dark) .transfer-progress-days {
        color: var(--gray-500);
    }

    /* ── error ── */
    .transfer-error-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 1rem;
        gap: 1.5rem;
    }

    .transfer-error-icon {
        width: 4rem;
        height: 4rem;
        color: var(--danger-500);
    }

    .transfer-error-text {
        text-align: center;
    }

    .transfer-error-text h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--danger-600);
        margin: 0 0 0.5rem 0;
    }

    :is(.dark) .transfer-error-text h3 {
        color: var(--danger-400);
    }

    .transfer-error-text p {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin: 0;
    }

    .transfer-error-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: var(--danger-500);
        color: #fff;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 0.5rem;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s;
    }

    .transfer-error-btn:hover {
        background: var(--danger-600);
    }
</style>

@if($isError)
    <div class="transfer-error-wrapper">
        <svg class="transfer-error-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>

        <div class="transfer-error-text">
            <h3>Falha ao sincronizar vendas</h3>
            <p>Ocorreu um erro ao buscar os dados. Volte ao passo anterior e tente novamente.</p>
        </div>

        <a href="{{ request()->url() }}" class="transfer-error-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1rem;height:1rem;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Tentar novamente
        </a>
    </div>
@else
    <div class="transfer-progress-wrapper">
        <x-filament::loading-indicator style="width: 4rem; height: 4rem; color: var(--primary-500);" />

        <div class="transfer-progress-text-center">
            <h3>{{ $statusText }}</h3>
            <p>Aguarde enquanto buscamos e calculamos as vendas do período.</p>
        </div>

        <div class="transfer-progress-bar-container">
            <div class="transfer-progress-bar-header">
                <span class="transfer-progress-bar-label">Progresso</span>
                <span class="transfer-progress-bar-value">{{ $progress }}%</span>
            </div>
            <div class="transfer-progress-bar-track">
                <div class="transfer-progress-bar-fill" style="width: {{ $progress }}%"></div>
            </div>
            @if($totalDays > 0)
                <p class="transfer-progress-days">
                    {{ $processedDays }} de {{ $totalDays }} dias processados
                </p>
            @endif
        </div>
    </div>
@endif
