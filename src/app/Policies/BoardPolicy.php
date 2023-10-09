<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoardPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Board $board)
    {
        return $board->subscription->owner_id === $user->id;
    }

    public function update(User $user, Board $board)
    {
        return $board->subscription->owner_id === $user->id;
    }

    public function delete(User $user, Board $board)
    {
        return $board->subscription->owner_id === $user->id;
    }

    public function getTasks(User $user, Board $board)
    {
        return $board->subscription->owner_id === $user->id;
    }

    public function createTask(User $user, Board $board)
    {
        return $board->subscription->owner_id === $user->id;
    }
}
