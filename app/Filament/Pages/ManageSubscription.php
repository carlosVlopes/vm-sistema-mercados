<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Stripe\StripeClient;

class ManageSubscription extends Page implements HasActions
{
    use InteractsWithActions;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Minha Assinatura';

    protected static ?string $title = 'Minha Assinatura';

    protected static ?string $slug = 'assinatura';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.manage-subscription';

    public ?array $subscription = null;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->loadSubscription();
    }

    public function loadSubscription(): void
    {
        $user = auth()->user();

        if (! $user->stripe_subscription_id) {
            $this->subscription = [
                'status' => $user->subscription_status,
            ];

            return;
        }

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));

            $sub = $stripe->subscriptions->retrieve($user->stripe_subscription_id);

            $this->subscription = [
                'status' => $sub->status,
                'cancel_at_period_end' => $sub->cancel_at_period_end,
                'current_period_start' => $sub->items->data[0]->current_period_start,
                'current_period_end' => $sub->items->data[0]->current_period_end,
                'canceled_at' => $sub->canceled_at,
                'amount' => $sub->items->data[0]->price->unit_amount ?? null,
                'currency' => $sub->items->data[0]->price->currency ?? 'brl',
                'interval' => $sub->items->data[0]->price->recurring->interval ?? 'month',
            ];

            if ($user->subscription_status !== $sub->status) {
                $user->subscription_status = $sub->status;
                $user->save();
            }
        } catch (\Exception $e) {
            $this->subscription = [
                'status' => $user->subscription_status,
            ];
            $this->errorMessage = 'Não foi possível carregar os detalhes da assinatura do Stripe.';
        }
    }

    public function cancelSubscriptionAction(): Action
    {
        return Action::make('cancelSubscription')
            ->label('Cancelar Assinatura')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->outlined()
            ->requiresConfirmation()
            ->modalHeading('Cancelar Assinatura')
            ->modalDescription('Tem certeza que deseja cancelar sua assinatura? Você continuará com acesso até o final do período atual.')
            ->modalSubmitActionLabel('Sim, cancelar assinatura')
            ->modalCancelActionLabel('Voltar')
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalIconColor('danger')
            ->action(function () {
                $user = auth()->user();

                if (! $user->stripe_subscription_id) {
                    return;
                }

                try {
                    $stripe = new StripeClient(config('services.stripe.secret'));

                    $stripe->subscriptions->update($user->stripe_subscription_id, [
                        'cancel_at_period_end' => true,
                    ]);

                    $this->loadSubscription();

                    Notification::make()
                        ->success()
                        ->title('Cancelamento agendado')
                        ->body('Sua assinatura será cancelada ao final do período atual.')
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Erro')
                        ->body('Não foi possível cancelar a assinatura. Tente novamente.')
                        ->send();
                }
            });
    }

    public function resumeSubscription(): void
    {
        $user = auth()->user();

        if (! $user->stripe_subscription_id) {
            return;
        }

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));

            $stripe->subscriptions->update($user->stripe_subscription_id, [
                'cancel_at_period_end' => false,
            ]);

            $this->loadSubscription();

            Notification::make()
                ->success()
                ->title('Assinatura reativada')
                ->body('O cancelamento foi revertido. Sua assinatura continuará normalmente.')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Erro')
                ->body('Não foi possível reativar a assinatura. Tente novamente.')
                ->send();
        }
    }

    public function getStatusLabel(): string
    {
        if (($this->subscription['status'] ?? '') === 'active' && ($this->subscription['cancel_at_period_end'] ?? false)) {
            return 'Cancelamento Agendado';
        }

        return match ($this->subscription['status'] ?? 'pending') {
            'active' => 'Ativa',
            'past_due' => 'Pagamento Pendente',
            'canceled' => 'Cancelada',
            'incomplete' => 'Incompleta',
            'incomplete_expired' => 'Expirada',
            'trialing' => 'Em Teste',
            'unpaid' => 'Não Paga',
            'paused' => 'Pausada',
            'pending' => 'Aguardando Pagamento',
            default => 'Desconhecido',
        };
    }

    public function getStatusColor(): string
    {
        if (($this->subscription['status'] ?? '') === 'active' && ($this->subscription['cancel_at_period_end'] ?? false)) {
            return 'warning';
        }

        return match ($this->subscription['status'] ?? 'pending') {
            'active' => 'success',
            'past_due' => 'warning',
            'trialing' => 'info',
            'canceled', 'incomplete_expired', 'unpaid' => 'danger',
            default => 'gray',
        };
    }
}
