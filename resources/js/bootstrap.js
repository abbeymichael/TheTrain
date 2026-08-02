// Livewire 4 / Alpine.js ship via the @livewire/livewire package.
// No axios needed for this project — Livewire uses its own fetch layer.

// CSRF token for any fetch() calls
window._token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
