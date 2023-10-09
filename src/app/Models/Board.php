<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Askedio\SoftCascade\Traits\SoftCascadeTrait;

class Board extends Model
{
    use HasFactory,SoftDeletes,SoftCascadeTrait;//【Laravel】onDelete(‘cascade’)してるのにリレーションデータが論理削除されない https://zakkuri.life/laravel-ondelete-cascade-problem/

    protected $fillable = [
        'title',
        'subscription_id'
    ];

    protected $softCascade = ['tasks'];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function tasksOnlyTrashed(): HasMany
    {
        return $this->hasMany(Task::class)->onlyTrashed();
    }
}