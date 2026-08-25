<?php

namespace App\Support;

class ToastCollector
{
    private const SESSION_KEY = 'pending_toasts';

    public function push(string $type, string $title, string $message): void
    {
        session()->push(self::SESSION_KEY, ['type' => $type, 'title' => $title, 'message' => $message]);
    }

    /**
     * @return array<int, array{type: string, title: string, message: string}>
     */
    public function flush(): array
    {
        return session()->pull(self::SESSION_KEY, []);
    }
}
