@props(['href', 'label' => 'Voltar'])

<a href="{{ $href }}" title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->merge(['class' => 'btn btn-outline-secondary btn-icon bg-body']) }}>
    <i class="bi bi-arrow-left" aria-hidden="true"></i>
</a>
