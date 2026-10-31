<?php

namespace App\Services\Admin;

use App\Enums\XpSourceType;
use App\Models\XpTransaction;
use App\Repositories\TaskEventRepository;
use App\Repositories\UserRepository;
use App\Repositories\XpTransactionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class XpAuditService
{
    public function __construct(
        private XpTransactionRepository $transactions,
        private TaskEventRepository $taskEvents,
        private UserRepository $users,
    ) {}

    /**
     * @param  array{user_id: ?int, source: ?int, from: ?string, to: ?string}  $filters
     * @return array{transactions: LengthAwarePaginator, taskIds: Collection<int, int>, total: int, users: Collection}
     */
    public function audit(array $filters, int $perPage): array
    {
        $transactions = $this->transactions->paginateAudit($filters, $perPage);

        return [
            'transactions' => $transactions,
            'taskIds' => $this->taskIdsOf($transactions->getCollection()),
            'total' => $this->transactions->sumAudit($filters),
            'users' => $this->users->all()->sortBy('name')->values(),
        ];
    }

    /**
     * @param  array{user_id: ?int, source: ?int, from: ?string, to: ?string}  $filters
     * @return iterable<int, array<int, scalar|null>>
     */
    public function exportRows(array $filters): iterable
    {
        return $this->transactions->lazyAudit($filters)->map(fn (XpTransaction $transaction) => [
            $transaction->created_at->format('d/m/Y H:i:s'),
            $transaction->user?->name,
            $transaction->amount,
            $transaction->source_type->label(),
            $transaction->description,
        ]);
    }

    /** @return Collection<int, int> id da tarefa de origem indexado pelo id da transação */
    private function taskIdsOf(Collection $transactions): Collection
    {
        $eventTaskIds = $this->taskEvents->taskIdsByEventIds(
            $transactions->where('source_type', XpSourceType::TASK_EVENT)->pluck('source_id')->all(),
        );

        return $transactions
            ->mapWithKeys(fn (XpTransaction $transaction) => [$transaction->id => match ($transaction->source_type) {
                XpSourceType::TASK => $transaction->source_id,
                XpSourceType::TASK_EVENT => $eventTaskIds->get($transaction->source_id),
                default => null,
            }])
            ->filter();
    }
}
