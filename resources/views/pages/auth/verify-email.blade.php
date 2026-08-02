<?php
// Livewire 4 SFC — Auth\VerifyEmail
use Livewire\Volt\Component;
use Illuminate\Auth\Events\Verified;

new class extends Component {
    public string $status = '';

    public function resend(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirect(route('user.dashboard'), navigate: true);
            return;
        }

        auth()->user()->sendEmailVerificationNotification();

        $this->status = 'A new verification link has been sent to your email address.';
    }

    public function mount(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirect(route('user.dashboard'), navigate: true);
        }
    }
}; ?>

<x-layouts.auth>
    <x-slot:title>Verify Email</x-slot:title>

    <div class="bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(84,106,123,0.15)] border border-[#e4e7e5] p-8 text-center">
        <!-- Icon -->
        <div class="w-16 h-16 rounded-full bg-[#c6ebd5] flex items-center justify-center mx-auto mb-5">
            <span class="material-symbols-outlined text-[#416352] text-3xl" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">mark_email_unread</span>
        </div>

        <h2 class="text-2xl font-semibold text-[#1b1c1a] mb-2" style="font-family:'Source Serif 4',serif;">Check your inbox</h2>
        <p class="text-sm text-[#727973] mb-6">
            We've sent a verification link to <strong class="text-[#1b1c1a]">{{ auth()->user()->email }}</strong>. Click the link in the email to activate your account.
        </p>

        @if ($status)
            <div class="mb-4 flex items-center gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-sm px-4 py-3 rounded-lg">
                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">check_circle</span>
                {{ $status }}
            </div>
        @endif

        <div class="space-y-3">
            <button wire:click="resend"
                class="w-full bg-[#416352] text-white font-semibold text-sm py-3 rounded-xl hover:bg-[#2e4a3d] active:scale-[0.98] transition-all shadow-sm flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="resend">Resend Verification Email</span>
                <span wire:loading wire:target="resend">Sending…</span>
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-sm text-[#727973] hover:text-[#416352] transition-colors py-2">
                    Sign out of this account
                </button>
            </form>
        </div>
    </div>
</x-layouts.auth>
