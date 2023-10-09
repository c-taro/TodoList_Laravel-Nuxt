<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Task $task, Board $board)
    {
        return $task->board->id === $board->id
            && $board->subscription->owner_id === $user->id;
    }

    public function update(User $user, Task $task, Board $board)
    {
        return $task->board->id === $board->id
            && $board->subscription->owner_id === $user->id;
    }

    public function delete(User $user, Task $task, Board $board)
    {
        return $task->board->id === $board->id
            && $board->subscription->owner_id === $user->id;
    }

    public function restore(User $user, Task $task, Board $board)
    {
        return $task->board->id === $board->id && $board->subscription->owner_id === $user->id;
    }
}
