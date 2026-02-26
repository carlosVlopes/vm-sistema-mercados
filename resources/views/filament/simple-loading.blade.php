<style>
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

    /* ── success ── */
    .transfer-success-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 1rem;
        gap: 1.5rem;
    }

    .transfer-success-icon {
        width: 4rem;
        height: 4rem;
        color: var(--success-500);
    }

    .transfer-success-text {
        text-align: center;
    }

    .transfer-success-text h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--success-600);
        margin: 0 0 0.5rem 0;
    }

    :is(.dark) .transfer-success-text h3 {
        color: var(--success-400);
    }

    .transfer-success-text p {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin: 0;
    }

    .transfer-success-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: var(--success-500);
        color: #fff;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 0.5rem;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s;
    }

    .transfer-success-btn:hover {
        background: var(--success-600);
    }
</style>

@if($status === 'error')
    <div class="transfer-error-wrapper">
        <svg class="transfer-error-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>

        <div class="transfer-error-text">
            <h3>Falha ao validar seu token</h3>
            <p>Ocorreu um erro ao validar seu token, por favor verifique o token e tente novamente.</p>
        </div>

        <a href="{{ route('filament.painel.pages.setup-account') }}" class="transfer-error-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1rem;height:1rem;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Tentar novamente
        </a>
    </div>
@elseif($status === 'success')
    <div class="transfer-success-wrapper">
        <svg class="transfer-success-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>

        <div class="transfer-success-text">
            <h3>Token validado com sucesso!</h3>
            @if(!$isFirstSetup)
                <p>Suas configurações foram salvas.</p>
            @else
                <p>Suas configurações foram salvas. Você já pode acessar o sistema.</p>
            @endif
        </div>

        <a href="{{ route('filament.painel.pages.dashboard') }}" class="transfer-success-btn">
            @if(!$isFirstSetup)
                <p>Voltar</p>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1rem;height:1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
                <p>Acessar o sistema</p>
            @endif
        </a>
    </div>
@else
    <div class="transfer-progress-wrapper">
        <x-filament::loading-indicator style="width: 8rem; height: 8rem; color: var(--primary-500);" />

        <div class="transfer-progress-text-center">
            <h3>Validando Token...</h3>
            <p>Aguarde enquanto validamos o seu token.</p>
        </div>
    </div>
@endif
