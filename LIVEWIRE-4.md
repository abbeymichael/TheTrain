# Livewire 4 — Agent Reference

Rules for this project: **Livewire v4**. Volt is gone. Do not write `Livewire\Volt\Component`, do not use `Volt::route()`, do not suggest installing `livewire/volt`. Livewire 4 SFCs replaced Volt entirely.

---

## 1. The three component formats

Livewire 4 supports three formats. Same component name, same `<livewire:... />` tag — you can switch formats without touching any Blade template or route.

| Format | Where it lives | When to use |
|---|---|---|
| **SFC** (single-file, **default**) | one `.blade.php` file, class + view combined | default choice for most components |
| **MFC** (multi-file) | a directory with separate `.php`/`.blade.php`/`.js`/`.css` files | large/complex components, heavy JS, better IDE navigation |
| **Class-based** (old v2/v3 style) | `app/Livewire/X.php` + `resources/views/livewire/x.blade.php` | only when explicitly migrating v3 code or team insists on classic structure |

**Default in this project is SFC.** Don't generate class-based components unless asked.

### SFC example
`php artisan make:livewire post.create` creates:

`resources/views/components/post/⚡create.blade.php`
```php
<?php

use Livewire\Component;

new class extends Component {
    public $title = '';

    public function save()
    {
        // ...
    }
};
?>

<div>
    <input wire:model="title" type="text">
    <button wire:click="save">Save Post</button>
</div>
```
Component name resolves to `post.create` regardless of the ⚡ emoji or file location — used as `<livewire:post.create />`.

### MFC example
`php artisan make:livewire post.create --mfc` creates:
```
resources/views/components/post/⚡create/
├── create.php          # PHP class
├── create.blade.php    # Blade template
├── create.js           # optional
├── create.css          # optional, scoped
├── create.global.css   # optional, global
└── create.test.php     # optional (--test)
```
Same component name (`post.create`), same tag usage.

### Converting between formats
```bash
php artisan livewire:convert post.create          # auto-detect, toggles SFC<->MFC
php artisan livewire:convert post.create --mfc
php artisan livewire:convert post.create --sfc
```

### The ⚡ emoji
`make:livewire` prefixes generated view-based (SFC/MFC) filenames with a ⚡ emoji by default, purely so they stand out in the file tree. It is stripped from the component name automatically — never include it when referencing a component in `<livewire:... />` tags, routes, or `Route::livewire()`. Disable it project-wide with:
```php
// config/livewire.php
'make_command' => [
    'emoji' => false,
],
```

### Command flags cheat-sheet
```bash
php artisan make:livewire post.create            # SFC (default)
php artisan make:livewire post.create --mfc       # MFC
php artisan make:livewire post.create --class     # old-style class + view
php artisan make:livewire post.create --type=sfc  # explicit
php artisan make:livewire post.create --test      # + Pest test
php artisan make:livewire pages::post.create      # a page component (see §3)
```

---

## 2. Volt is dead — do this instead

If you see old code or are tempted to reach for Volt, **don't**. Livewire 4 SFCs are, syntactically, the same as Volt class components.

| Volt (remove) | Livewire 4 (use) |
|---|---|
| `use Livewire\Volt\Component;` | `use Livewire\Component;` |
| `Volt::route('/dashboard', 'dashboard');` | `Route::livewire('/dashboard', 'dashboard');` |
| `Volt::test('counter')` | `Livewire::test('counter')` |
| `app/Providers/VoltServiceProvider.php` | delete it |
| `composer require livewire/volt` | `composer remove livewire/volt` |

Volt **functional** API (`state()`, `mount()` closures, etc.) has no direct v4 equivalent — convert those to real class-based SFCs.

---

## 3. Routing, layouts, and page titles

### Routing a component as a full page
Always use `Route::livewire()`, not the old `Route::get('/x', X::class)` style — it's required for SFC/MFC page components to render correctly.
```php
use Illuminate\Support\Facades\Route;

Route::livewire('/posts/create', 'pages::post.create');
```

### Page components live under `pages::`
Use the `pages::` namespace to keep pages separate from reusable components:
```bash
php artisan make:livewire pages::post.create
```
Creates `resources/views/pages/post/⚡create.blade.php`, referenced as `pages::post.create`.

### Layout
Livewire looks for `layouts::app` → `resources/views/layouts/app.blade.php` by default. Generate it with:
```bash
php artisan livewire:layout
```
It must contain a `{{ $slot }}`:
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>
```

Change the default layout globally:
```php
// config/livewire.php
'component_layout' => 'layouts::dashboard',
```

Set a layout per component:
```php
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::dashboard')] class extends Component {
    // ...
};
```
or fluently in `render()`:
```php
public function render()
{
    return $this->view()->layout('layouts::dashboard');
}
```

### Page title
Layout must render `{{ $title ?? config('app.name') }}` in `<title>`. Then on the component:
```php
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create post')] class extends Component {
    // ...
};
```
For a dynamic title, use the fluent method instead of the attribute:
```php
public function render()
{
    return $this->view()->title('Editing: ' . $this->post->title);
}
```

### Extra named layout slots
Define slots in the layout (besides `$slot`), then set them from the component view with `<x-slot:name>` **outside the root element**:
```blade
<x-slot:lang>fr</x-slot>

<div>
    <!-- component content -->
</div>
```

---

## 4. Config reference (v4 keys, renamed from v3)

```php
// config/livewire.php

'component_layout' => 'layouts::app',      // was 'layout' in v3
'component_placeholder' => 'livewire.placeholder', // was 'lazy_placeholder'
'smart_wire_keys' => true,                 // default changed, still add wire:key in loops manually

'component_locations' => [
    resource_path('views/components'),
    resource_path('views/livewire'),
],

'component_namespaces' => [
    'layouts' => resource_path('views/layouts'), // built-in
    'pages' => resource_path('views/pages'),     // built-in
],

'make_command' => [
    'type' => 'sfc',  // 'sfc' | 'mfc' | 'class'
    'emoji' => true,
],
```

---

## 5. Quick don'ts for agents

- ❌ Don't generate `Livewire\Volt\Component` anywhere.
- ❌ Don't use `Volt::route()` or `Volt::test()`.
- ❌ Don't default to class-based components (`app/Livewire/...`) unless told to.
- ❌ Don't leave `<livewire:component-name>` unclosed — v4 requires self-closing tags (`<livewire:component-name />`) now that slots are supported. Unclosed tags will swallow following markup as slot content.
- ❌ Don't include the ⚡ emoji when writing a component name in a tag, route, or `Route::livewire()` call — it's a filename-only convention.
- ❌ Don't set layout/title via v3 config keys (`layout`, `lazy_placeholder`) — use `component_layout` / `component_placeholder`.
