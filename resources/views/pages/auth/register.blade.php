<?php
// Livewire 4 SFC — Auth\Register
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

new
#[Layout('layouts::auth')]
#[Title('Create Account')]
class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $this->validate([
            'name'                  => ['required', 'string', 'min:2', 'max:100'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'phone'                 => ['nullable', 'string', 'max:30'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'phone'    => $this->phone ?: null,
            'password' => bcrypt($this->password),
            'role'     => 'user',
            'status'   => 'pending',
        ]);

        event(new Registered($user));

        auth()->login($user);

        $this->redirect(route('verify-email'), navigate: true);
    }
}; ?>

<div>

    <div class="bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(84,106,123,0.15)] border border-[#e4e7e5] p-8">
        <h2 class="text-2xl font-semibold text-[#1b1c1a] mb-1" style="font-family:'Source Serif 4',serif;">Begin your journey</h2>
        <p class="text-sm text-[#727973] mb-6">Create a free account to book restorative retreats.</p>

        <!-- Status note -->
        <div class="mb-5 flex gap-2 bg-[#f0faf5] border border-[#c6ebd5] text-[#2e4a3d] text-xs px-4 py-3 rounded-lg">
            <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">info</span>
            <span>Registration is free. Your account will go through a quick approval before you can book trips — typically within 24 hours.</span>
        </div>

        <form wire:submit="register" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-[#414844] mb-1.5" for="name">Full name</label>
                <input wire:model="name" id="name" type="text" autocomplete="name" required
                    class="w-full px-4 py-3 rounded-xl border border-[#c1c8c2] bg-[#fbf9f6] text-sm text-[#1b1c1a] placeholder-[#9ba39c] focus:outline-none focus:ring-2 focus:ring-[#416352] focus:border-transparent transition-shadow"
                    placeholder="Your full name" />
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#414844] mb-1.5" for="email">Email address</label>
                <input wire:model="email" id="email" type="email" autocomplete="email" required
                    class="w-full px-4 py-3 rounded-xl border border-[#c1c8c2] bg-[#fbf9f6] text-sm text-[#1b1c1a] placeholder-[#9ba39c] focus:outline-none focus:ring-2 focus:ring-[#416352] focus:border-transparent transition-shadow"
                    placeholder="you@example.com" />
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#414844] mb-1.5" for="phone">
                    Phone number <span class="text-[#9ba39c] font-normal">(optional)</span>
                </label>
                <input wire:model="phone" id="phone" type="tel" autocomplete="tel"
                    class="w-full px-4 py-3 rounded-xl border border-[#c1c8c2] bg-[#fbf9f6] text-sm text-[#1b1c1a] placeholder-[#9ba39c] focus:outline-none focus:ring-2 focus:ring-[#416352] focus:border-transparent transition-shadow"
                    placeholder="+1 (000) 000-0000" />
                @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#414844] mb-1.5" for="password">Password</label>
                <input wire:model="password" id="password" type="password" autocomplete="new-password" required
                    class="w-full px-4 py-3 rounded-xl border border-[#c1c8c2] bg-[#fbf9f6] text-sm text-[#1b1c1a] placeholder-[#9ba39c] focus:outline-none focus:ring-2 focus:ring-[#416352] focus:border-transparent transition-shadow"
                    placeholder="At least 8 characters" />
                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#414844] mb-1.5" for="password_confirmation">Confirm password</label>
                <input wire:model="password_confirmation" id="password_confirmation" type="password" autocomplete="new-password" required
                    class="w-full px-4 py-3 rounded-xl border border-[#c1c8c2] bg-[#fbf9f6] text-sm text-[#1b1c1a] placeholder-[#9ba39c] focus:outline-none focus:ring-2 focus:ring-[#416352] focus:border-transparent transition-shadow"
                    placeholder="Re-enter your password" />
                @error('password_confirmation') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                class="w-full bg-[#416352] text-white font-semibold text-sm py-3 rounded-xl hover:bg-[#2e4a3d] active:scale-[0.98] transition-all shadow-sm flex items-center justify-center gap-2 mt-2">
                <span wire:loading.remove wire:target="register">Create Account</span>
                <span wire:loading wire:target="register">Creating account…</span>
                <span wire:loading.remove wire:target="register" class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;">arrow_forward</span>
            </button>
        </form>
    </div>

    <p class="text-center text-sm text-[#727973] mt-6">
        Already have an account?
        <a href="{{ route('login') }}" class="text-[#416352] font-semibold hover:text-[#2e4a3d] transition-colors">Sign in</a>
    </p>
</div>
