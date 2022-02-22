<?php

namespace App\Modules\Banking\Models;

use Brick\Math\BigInteger;
use Database\Factories\TransactionFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * Model Transaction
 *
 * @property int id
 * @property int account_id
 * @property BigInteger amount
 * @property string transaction_type
 * @property string type DEBIT|CREDIT
 * @property string status DONE|FAILED
 * @property string time
 *
 * @property Account account
 * @property mixed created_at
 * @property mixed updated_at
 *
 */
class Transaction extends Model
{
    use HasFactory;

    public const TYPE_DEBIT = 'DEBIT';
    public const TYPE_CREDIT = 'CREDIT';

    public const STATUS_DONE = 'DONE';
    public const STATUS_FAILED = 'FAILED';

    protected $fillable = [];

    protected $hidden = [
        'laravel_through_key'
    ];

    protected static function newFactory(): TransactionFactory
    {
        return TransactionFactory::new();
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!($model->status == self::STATUS_FAILED)) {
                $method = ($model->type == self::TYPE_CREDIT) ? 'addCredit' : 'addDebit';
                $model->account->$method($model->amount);
            }
        });
    }


    public static function transactionsModels(): array
    {
        return [
            Transfer::class
        ];
    }

    public static function getTransactionsTypes(): array
    {
        $types = [];
        foreach (self::transactionsModels() as $tModel) {
            $types[$tModel::TRANSACTION_TYPE_LABEL] = $tModel::TRANSACTION_TYPE;
        }

        return $types;
    }

    /**
     * @throws Exception
     */
    public function createPreTransaction($account_id, $amount, $transaction_type, $type)
    {
        $this->account_id = $account_id;
        $this->amount = $amount;
        $this->transaction_type = $transaction_type;
        $this->type = $type;
        $this->status = Transaction::STATUS_FAILED;
        if (!$this->save()) {
            throw new Exception("Failed To build pre-Transaction!!", 500);
        }
    }

    /**
     * @throws Exception
     */
    public function approveTransaction()
    {
        $this->status = Transaction::STATUS_DONE;
        if (!$this->save()) {
            throw new Exception("Failed To approve Transaction!!", 500);
        }
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

}
