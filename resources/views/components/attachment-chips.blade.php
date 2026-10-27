@props(['attachments', 'route'])

@if ($attachments->isNotEmpty())
    <div class="d-flex flex-wrap gap-2 mt-2">
        @foreach ($attachments as $attachment)
            <a href="{{ route($route, $attachment) }}" target="_blank" rel="noopener noreferrer" class="attachment-chip" title="{{ $attachment->original_name }} · {{ $attachment->human_size }}">
                @if ($attachment->isImage())
                    <img src="{{ route($route, $attachment) }}" alt="" class="attachment-chip-thumb" loading="lazy">
                @else
                    <i class="bi {{ $attachment->icon() }}" aria-hidden="true"></i>
                @endif
                <span class="text-truncate">{{ $attachment->original_name }}</span>
                <span class="text-body-secondary">{{ $attachment->human_size }}</span>
            </a>
        @endforeach
    </div>
@endif
