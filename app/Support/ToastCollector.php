<?php

namespace App\Support;

class ToastCollector
{
    /** @var array<int, array{type: string, title: string, message: string}> */
    private array $toasts = [];

    public function push(string $type, string $title, string $message): void
    {
        $this->toasts[] = ['type' => $type, 'title' => $title, 'message' => $message];
    }

    /**
     * @return array<int, array{type: string, title: string, message: string}>
     */
    public function flush(): array
    {
        $toasts = $this->toasts;
        $this->toasts = [];

        return $toasts;
    }
}
