<?php

namespace App\Modules\Banking\Models;

use Brick\Math\BigInteger;
use Database\Factories\TransferFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Finnotech\OAK\V2\Oak;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string track_id
 * @property string description
 * @property string destinationFirstname
 * @property string destinationLastname
 * @property string destinationNumber
 * @property string inquiryDate
 * @property string inquiryTime
 * @property string inquirySequence
 * @property string message
 * @property string paymentNumber
 * @property string refCode
 * @property string type
 * @property int transaction_id
 * @property Transaction transaction
 */
class Transfer extends Model
{
    use HasFactory;

    public const TRANSACTION_TYPE= 1;
    public const TRANSACTION_TYPE_LABEL= 'TRANSFER';

    public const TRACK_ID_PREFIX = 'ttd-';
    public const PAYA_TRANSFER_MAX_LIMIT = 500_000_000;


    protected $guarded = [];

    protected $hidden = [
        'laravel_through_key'
     ];

    public $timestamps = false;

    private int $accountId;
    private BigInteger $amount;
    private string $status;

    protected static function newFactory(): TransferFactory
    {
        return TransferFactory::new();
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            if (!isset($model->transaction_id)){
                $transaction = new Transaction;
                $transaction->createPreTransaction(
                    $model->accountId,
                    $model->amount,
                    self::TRANSACTION_TYPE,
                    Transaction::TYPE_DEBIT
                );
                $model->transaction_id = $transaction->id;
            }
         });

        self::created(function($model){
            if(!($model->status == Transaction::STATUS_FAILED)){
                $model->transaction->approveTransaction();
            }
        });
    }


    /**
     * @throws Exception
     */
    public function createTransfer(Account $account, $request)
    {
        $oak = new Oak;
        $response =  $oak->transferTo($request, $account);
        // @todo handle exceptions, errors and etc.

        $result = $response['result'];
        $this->status = $response['status'];

        $this->accountId = $account->id;
        $this->amount = $result['amount'];
        $this->description = $result['description'];
        $this->destinationFirstname = $result['destinationFirstname'];
        $this->destinationLastname = $result['destinationLastname'];
        $this->destinationNumber = $result['destinationNumber'];
        $this->inquiryDate = $result['inquiryDate'];
        $this->inquiryTime = $result['inquiryTime'];
        $this->inquirySequence = $result['inquirySequence'];
        $this->message = $result['message'];
        $this->paymentNumber = $result['paymentNumber'];
        $this->refCode = $result['refCode'];
        $this->type = $result['type'];
        $this->track_id = $response['trackId'];

        $this->save();
    }



    public static function generateTransferTrackId()
    {
        $statement = Carbon::now()->format('ynHij');
        return Transfer::TRACK_ID_PREFIX .  $statement . rand(100, 999);
    }

    public function getPayaCycleEndPoint(): string
    {
        $time = $this->transaction->created_at;
        $bank = $this->transaction->account->bank;

        $firstCycleStart = Carbon::createFromTimeString('13:15');
        $firstCycleEnd =  (! $bank == Bank::PARSAIN)
            ? Carbon::createFromTimeString('03:14:59')
            : Carbon::createFromTimeString('10:14:59');
        $firstCycleEnd = ($time->between($firstCycleStart, Carbon::createFromTimeString('23:59:59'))) ?
           $firstCycleEnd : $firstCycleEnd->addDay() ;
        $firstCycleTime = Carbon::createFromTimeString('03:45');

        $secondCycleStart = Carbon::createFromTimeString('03:15');
        $secondCycleEnd =  Carbon::createFromTimeString('10:14:59');
        $secondCycleTime = Carbon::createFromTimeString('10:45');

        $thirdCycleTime = Carbon::createFromTimeString('13:45');

        if ($time->between($firstCycleStart, $firstCycleEnd)){
            return $firstCycleTime;
        }else if(!$bank == Bank::PARSAIN && $time->between($secondCycleStart, $secondCycleEnd)){
            return $secondCycleTime;
        } else {
            return $thirdCycleTime;
        }
    }


    // public static function fetchUserTranfers(User $user) {
    //     return Transfer::select('transfers.*')
    //         ->join('transactions', 'transactions.id', '=', 'transfers.transaction_id')
    //         ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
    //         ->where('accounts.owner_id', $user->id)
    //         ->get();
    // }



    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }


}
