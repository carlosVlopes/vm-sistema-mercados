<x-filament-panels::page>

    @if($errorMessage)
        <x-filament::section>
            <div style="display: flex; align-items: center; gap: 0.75rem; color: #d97706;">
                <x-heroicon-o-exclamation-triangle style="width: 1.25rem; height: 1.25rem; flex-shrink: 0;" />
                <span style="font-size: 0.875rem;">{{ $errorMessage }}</span>
            </div>
        </x-filament::section>
    @endif

    {{-- Plan Hero Card --}}
    <x-filament::section>
        <div style="display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1rem;">
                <div style="width: 3rem; height: 3rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; background: rgba(var(--primary-500), 0.1); flex-shrink: 0;">
                    <x-heroicon-o-sparkles style="width: 1.5rem; height: 1.5rem; color: rgb(var(--primary-500));" />
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0; color: var(--fi-body-text-color, #FC6E20);">Plano Mensal</h3>
                    <p style="margin: 0.25rem 0 0; font-size: 0.875rem; color: #6b7280;">Acesso completo ao nosso sistema</p>

                    @if(isset($subscription['amount']))
                        <div style="margin-top: 0.75rem;">
                            <span style="font-size: 1.5rem; font-weight: 800;">
                                R$ {{ number_format($subscription['amount'] / 100, 2, ',', '.') }}
                            </span>
                            <span style="font-size: 0.875rem; font-weight: 500; color: #9ca3af;">/mês</span>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <x-filament::badge :color="$this->getStatusColor()" size="lg">
                    {{ $this->getStatusLabel() }}
                </x-filament::badge>
            </div>
        </div>
    </x-filament::section>

    {{-- Subscription Details --}}
    @if(isset($subscription['current_period_start']))
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
            {{-- Period Start --}}
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; background: rgba(107, 114, 128, 0.1); flex-shrink: 0;">
                        <x-heroicon-o-calendar style="width: 1.125rem; height: 1.125rem; color: #6b7280;" />
                    </div>
                    <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280;">Início do Período</span>
                </div>
                <p style="font-size: 1rem; font-weight: 600; margin: 0;">
                    {{ \Carbon\Carbon::createFromTimestamp($subscription['current_period_start'])->format('d/m/Y') }}
                </p>
            </x-filament::section>

            {{-- Next Billing --}}
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; background: rgba(107, 114, 128, 0.1); flex-shrink: 0;">
                        <x-heroicon-o-clock style="width: 1.125rem; height: 1.125rem; color: #6b7280;" />
                    </div>
                    <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280;">
                        {{ ($subscription['cancel_at_period_end'] ?? false) ? 'Acesso Até' : 'Próxima Cobrança' }}
                    </span>
                </div>
                <p style="font-size: 1rem; font-weight: 600; margin: 0; color: {{ ($subscription['cancel_at_period_end'] ?? false) ? '#dc2626' : '' }};">
                    {{ \Carbon\Carbon::createFromTimestamp($subscription['current_period_end'])->format('d/m/Y') }}
                </p>
            </x-filament::section>

            {{-- Renewal --}}
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; background: rgba(107, 114, 128, 0.1); flex-shrink: 0;">
                        <x-heroicon-o-arrow-path style="width: 1.125rem; height: 1.125rem; color: #6b7280;" />
                    </div>
                    <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280;">Renovação</span>
                </div>
                @if($subscription['cancel_at_period_end'] ?? false)
                    <p style="font-size: 1rem; font-weight: 600; margin: 0; color: #d97706;">Não renovará</p>
                @else
                    <p style="font-size: 1rem; font-weight: 600; margin: 0; color: #059669;">Automática</p>
                @endif
            </x-filament::section>
        </div>
    @endif

    {{-- Actions --}}
    @if(($subscription['status'] ?? '') === 'active')
        @if($subscription['cancel_at_period_end'] ?? false)
            {{-- Cancellation scheduled --}}
            <x-filament::section>
                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; background: rgba(217, 119, 6, 0.1); flex-shrink: 0;">
                            <x-heroicon-o-exclamation-triangle style="width: 1.25rem; height: 1.25rem; color: #d97706;" />
                        </div>
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 600; margin: 0; color: var(--fi-body-text-color, #FC6E20);">Cancelamento Agendado</h3>
                            <p style="margin: 0.25rem 0 0; font-size: 0.875rem; color: #6b7280;">
                                Sua assinatura será encerrada em
                                <strong>{{ \Carbon\Carbon::createFromTimestamp($subscription['current_period_end'])->format('d/m/Y') }}</strong>.
                                Reverta o cancelamento para manter seu acesso.
                            </p>
                        </div>
                    </div>
                    <div style="flex-shrink: 0;">
                        <x-filament::button
                            wire:click="resumeSubscription"
                            color="success"
                            icon="heroicon-o-arrow-path"
                            size="lg"
                        >
                            Manter Assinatura
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @else
            {{-- Active subscription --}}
            <x-filament::section>
                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div>
                        <h3 style="font-size: 0.875rem; font-weight: 600; margin: 0; color: var(--fi-body-text-color, #FC6E20);">Cancelar Assinatura</h3>
                        <p style="margin: 0.25rem 0 0; font-size: 0.875rem; color: #6b7280;">
                            Ao cancelar, você manterá o acesso até o final do período atual.
                        </p>
                    </div>
                    <div style="flex-shrink: 0;">
                        {{ $this->cancelSubscriptionAction }}
                    </div>
                </div>
            </x-filament::section>
        @endif
    @endif

    {{-- Reactivate for canceled users --}}
    @if(($subscription['status'] ?? '') === 'canceled')
        <x-filament::section>
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; background: rgba(220, 38, 38, 0.1); flex-shrink: 0;">
                        <x-heroicon-o-x-circle style="width: 1.25rem; height: 1.25rem; color: #dc2626;" />
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 600; margin: 0; color: var(--fi-body-text-color, #FC6E20);">Assinatura Cancelada</h3>
                        <p style="margin: 0.25rem 0 0; font-size: 0.875rem; color: #6b7280;">
                            Sua assinatura foi cancelada. Reative para continuar usando o sistema.
                        </p>
                    </div>
                </div>
                <div style="flex-shrink: 0;">
                    <a href="{{ route('assinatura.reativar') }}">
                        <x-filament::button
                            color="primary"
                            icon="heroicon-o-arrow-path"
                            size="lg"
                        >
                            Reativar Assinatura
                        </x-filament::button>
                    </a>
                </div>
            </div>
        </x-filament::section>
    @endif

</x-filament-panels::page>
