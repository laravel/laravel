<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Ramsey\Collection\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $type
 * @property bool $muted
 * @property array $options
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @property Collection $messages
 */
class Group extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Types.
     */
    public const TYPE_USER = 1;
    public const TYPE_GROUP = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'muted',
        'options',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * The users that belong to the group.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the messages for the group.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function isUserType(): bool
    {
        return $this->type == self::TYPE_USER;
    }

    public function isGroupType(): bool
    {
        return $this->type == self::TYPE_GROUP;
    }
}
