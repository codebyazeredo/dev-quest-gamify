@props(['href', 'label' => 'Voltar'])

<a href="{{ $href }}" title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->merge(['class' => 'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-ink-muted transition-colors hover:bg-line/20 hover:text-ink']) }}>
    <x-icon name="chevron-down" class="h-5 w-5 rotate-90" />
</a>
