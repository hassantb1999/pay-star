<?php

namespace App\Modules\Banking\Models;

use App\Models\User;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Modules\Banking\Models\Account
 *
 * @property int $id
 * @property int $owner_id
 * @property string $bank
 * @property int $credit
 * @property string|null $description
 * @property string $account_number
 * @property string $shaba_number
 *
 * @property Collection $transfers
 * @property Collection $transactions
 *
 * @property User $owner
 */
class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $hidden = [
        'laravel_through_key'
    ];

    protected static function newFactory(): AccountFactory
    {
        return AccountFactory::new();
    }

    public function addCredit($amount)
    {
        $this->credit += $amount;
        $this->save();
    }

    public function addDebit($amount)
    {
        $this->credit -= $amount;
        $this->save();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transfers(): HasManyThrough
    {
        return $this->hasManyThrough(Transfer::class, Transaction::class);
    }
}
