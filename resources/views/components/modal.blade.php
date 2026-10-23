@props(['title', 'size' => null])

<div
    x-data="{ show: false }"
    x-init="$nextTick(() => show = true)"
    x-on:keydown.escape.window="$dispatch('close-modal')"
>
    <div class="modal-backdrop fade" :class="{ show }"></div>

    <div class="modal fade d-block" :class="{ show }" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="modal-title" x-on:mousedown.self="$dispatch('close-modal')">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable {{ $size ? 'modal-'.$size : '' }}">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modal-title">{{ $title }}</h2>
                    <button type="button" class="btn-close" x-on:click="$dispatch('close-modal')" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
