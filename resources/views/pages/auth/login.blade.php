<?php
// Livewire 4 SFC — Auth\Login
use Livewire\Volt\Component;

new class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $error = '';

    public function login(): void
    {
        $this->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->error = 'These credentials do not match our records.';
            return;
        }

        session()->regenerate();

        $user = auth()->user();

        $this->redirect(match ($user->role) {
            'admin'      => route('admin.dashboard'),
            'specialist' => route('specialist.dashboard'),
            default      => route('user.dashboard'),
        }, navigate: true);
    }
}; ?>

<x-layouts.auth>
    <x-slot:title>Sign In</x-slot:title>

    <div class="bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(84,106,123,0.15)] border border-[#e4e7e5] p-8">
        <h2 class="text-2xl font-semibold text-[#1b1c1a] mb-1" style="font-family:'Source Serif 4',serif;">Welcome back</h2>
        <p class="text-sm text-[#727973] mb-6">Sign in to continue your journey.</p>

        @if ($error)
            <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">error</span>
                {{ $error }}
            </div>
        @endif

        <form wire:submit="login" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-[#414844] mb-1.5" for="email">Email address</label>
                <input wire:model="email" id="email" type="email" autocomplete="email" required
                    class="w-full px-4 py-3 rounded-xl border border-[#c1c8c2] bg-[#fbf9f6] text-sm text-[#1b1c1a] placeholder-[#9ba39c] focus:outline-none focus:ring-2 focus:ring-[#416352] focus:border-transparent transition-shadow"
                    placeholder="you@example.com" />
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-sm font-medium text-[#414844]" for="password">Password</label>
                    <a href="#" class="text-xs text-[#416352] hover:text-[#2e4a3d] font-medium transition-colors">Forgot password?</a>
                </div>
                <input wire:model="password" id="password" type="password" autocomplete="current-password" required
                    class="w-full px-4 py-3 rounded-xl border border-[#c1c8c2] bg-[#fbf9f6] text-sm text-[#1b1c1a] placeholder-[#9ba39c] focus:outline-none focus:ring-2 focus:ring-[#416352] focus:border-transparent transition-shadow"
                    placeholder="••••••••" />
                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2">
                <input wire:model="remember" id="remember" type="checkbox" class="w-4 h-4 text-[#416352] border-[#c1c8c2] rounded focus:ring-[#416352]" />
                <label for="remember" class="text-sm text-[#414844]">Keep me signed in</label>
            </div>

            <button type="submit"
                class="w-full bg-[#416352] text-white font-semibold text-sm py-3 rounded-xl hover:bg-[#2e4a3d] active:scale-[0.98] transition-all shadow-sm flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="login">Sign In</span>
                <span wire:loading wire:target="login">Signing in…</span>
                <span wire:loading.remove wire:target="login" class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_forward</span>
            </button>
        </form>
    </div>

    <p class="text-center text-sm text-[#727973] mt-6">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-[#416352] font-semibold hover:text-[#2e4a3d] transition-colors">Register here</a>
    </p>
</x-layouts.auth>
