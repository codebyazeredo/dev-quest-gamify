@props(['name' => 'files[]'])

<input
    type="file"
    name="{{ $name }}"
    multiple
    data-file-input
    data-max-files="{{ config('attachments.max_files') }}"
    data-max-size-kb="{{ config('attachments.max_size_kb') }}"
    data-extensions="{{ implode(',', config('attachments.extensions')) }}"
    {{ $attributes }}
>
