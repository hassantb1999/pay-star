<?php

namespace App\Modules\Banking\Models;

use App\Models\User;
use Brick\Math\BigInteger;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


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
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $owner
 * @method static \Database\Factories\AccountFactory factory(...$parameters)
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account query()
 * @method static Builder|Account whereAccountNumber($value)
 * @method static Builder|Account whereBank($value)
 * @method static Builder|Account whereCreatedAt($value)
 * @method static Builder|Account whereCredit($value)
 * @method static Builder|Account whereDeletedAt($value)
 * @method static Builder|Account whereDescription($value)
 * @method static Builder|Account whereId($value)
 * @method static Builder|Account whereOwnerId($value)
 * @method static Builder|Account whereShabaNumber($value)
 * @method static Builder|Account whereUpdatedAt($value)
 * @mixin Model
 */
class Account extends Model {

    use HasFactory, SoftDeletes;

    protected static function newFactory(): AccountFactory
    {
        return AccountFactory::new();
    }


    public function createAccount($ownerId, $bank,  $accountNumber, $shabaNumber,$credit = 0, $description=null){
        $this->owner_id = $ownerId;
        $this->bank = $bank;
        $this->credit = $credit;
        $this->description = $description ?? $bank . ' - ' . $accountNumber;
        $this->account_number = $accountNumber;
        $this->shaba_number = $shabaNumber;
        $this->save();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
