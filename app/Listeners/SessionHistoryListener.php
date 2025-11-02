<?php

namespace App\Listeners;

use App\Models\UserSessionHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SessionHistoryListener
{
    public function __construct(
        protected Request $request
    ) {}

    public function handle(Login|Logout $event): void
    {
        match (true) {
            $event instanceof Login => $this->handleLogin($event),
            $event instanceof Logout => $this->handleLogout($event),
        };
    }

    protected function handleLogin(Login $event): void
    {
        try {
            $sessionId = $this->request->session()->getId();
            $userAgent = $this->request->userAgent();

            UserSessionHistory::where('user_id', $event->user->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'ended_at' => now(),
                    'end_reason' => 'new_login',
                ]);

            UserSessionHistory::create([
                'session_id' => $sessionId,
                'user_id' => $event->user->id,
                'ip_address' => $this->request->ip(),
                'user_agent' => $userAgent,
                'device' => $this->getDevice($userAgent),
                'browser' => $this->getBrowser($userAgent),
                'location' => $this->request->ip(),
                'started_at' => now(),
                'is_active' => true,
            ]);

            activity('user')
                ->withProperties([
                    'user_id' => $event->user->getAuthIdentifier(),
                    'user_name' => $event->user->name ?? 'N/A',
                    'user_email' => $event->user->email ?? 'N/A',
                    'ip_address' => $this->request->ip(),
                    'user_agent' => $userAgent,
                    'timestamp' => now()->toISOString(),
                    'login_method' => 'filament_panel',
                ])
                ->event('login')
                ->log("Usuário {$event->user->name} fez login no sistema");
        } catch (\Exception $e) {
            Log::error('Erro ao processar login', [
                'error' => $e->getMessage(),
                'user_id' => $event->user->id ?? null,
            ]);
        }
    }

    protected function handleLogout(Logout $event): void
    {
        try {
            $sessionId = $this->request->session()->getId();

            $updated = UserSessionHistory::where('session_id', $sessionId)
                ->where('user_id', $event->user->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'ended_at' => now(),
                    'end_reason' => 'logout',
                ]);

            if ($updated === 0) {
                $updated = UserSessionHistory::where('user_id', $event->user->id)
                    ->where('is_active', true)
                    ->update([
                        'is_active' => false,
                        'ended_at' => now(),
                        'end_reason' => 'logout',
                    ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar logout', [
                'error' => $e->getMessage(),
                'user_id' => $event->user->id ?? null,
            ]);
        }
    }

    protected function getBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Chrome')) {
            return 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        } elseif (str_contains($userAgent, 'Safari')) {
            return 'Safari';
        } elseif (str_contains($userAgent, 'Edge')) {
            return 'Edge';
        }

        return 'Desconhecido';
    }

    protected function getDevice(string $userAgent): string
    {
        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone')) {
            return 'Mobile';
        } elseif (str_contains($userAgent, 'Tablet') || str_contains($userAgent, 'iPad')) {
            return 'Tablet';
        }

        return 'Desktop';
    }
}
