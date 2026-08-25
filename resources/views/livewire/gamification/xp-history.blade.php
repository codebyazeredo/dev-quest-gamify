<div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-gray-100">Recent activity</h3>

    @forelse ($transactions as $transaction)
        <div class="flex items-center justify-between border-b border-gray-100 py-2 text-sm last:border-0 dark:border-gray-700" wire:key="xp-{{ $transaction->id }}">
            <span class="text-gray-600 dark:text-gray-300">{{ $transaction->description }}</span>
            <x-xp-badge :amount="$transaction->amount" />
        </div>
    @empty
        <p class="text-sm text-gray-400">No activity yet.</p>
    @endforelse

    @if ($paginated)
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
