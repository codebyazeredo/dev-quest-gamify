<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\Url;

trait WithAdjustablePerPage
{
    #[Url(as: 'porPagina')]
    public int $perPage = 10;

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }
}
