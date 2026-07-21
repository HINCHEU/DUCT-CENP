<x-filament-panels::page>
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 1.5rem;">
        <h1 class="fi-header-heading text-gray-950 dark:text-white" style="font-size:1.5rem; font-weight:700; letter-spacing:-0.025em;">
            Dashboard
        </h1>
        <span style="font-size:0.9rem; color: var(--fi-color-gray-400, #9ca3af);">
            Welcome, <strong style="color: var(--fi-color-gray-200, #e5e7eb);">{{ auth()->user()->name }}</strong>
        </span>
    </div>

    {{ $this->content }}
</x-filament-panels::page>
