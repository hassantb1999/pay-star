<?php

namespace App\Models;

use App\Modules\Banking\Models\Account;
use App\Modules\Banking\Models\Transaction;
use App\Modules\Banking\Models\Transfer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int id
 * @property string name
 * @property string first_name
 * @property string last_name
 * @property string email
 *
 * @property Collection accounts
 * @property Collection transfers
 * @property Collection transactions
 */
class User extends Authenticate
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'laravel_through_key'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'owner_id', 'id');
    }

    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, Account::class, 'owner_id', 'account_id');
    }

    public function transfers(): HasManyThrough
    {
        return $this->hasManyThrough(Transfer::class, Transaction::class, 'accounts.owner_id', 'transaction_id', 'id', 'id')
            ->join('accounts', 'accounts.id', 'transactions.account_id');

    }
}
