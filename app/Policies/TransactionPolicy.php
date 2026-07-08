<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    public function cancel(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    public function review(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    public function pay(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }
}
