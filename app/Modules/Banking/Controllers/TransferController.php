<?php

namespace App\Modules\Banking\Controllers;

use App\Models\User;
use App\Modules\Banking\Models\Account;
use App\Modules\Banking\Models\Bank;
use App\Modules\Banking\Models\Transfer;
use App\Modules\Banking\Requests\StoreTransferRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Gate;

class TransferController extends BaseController
{

    public function index(Account $account = null)
    {
        if (isset($account)) {
            if (Gate::denies('access-account', $account)) {
                return response()->json([], 403);
            }
            return response($account->transfers, 200);
        }
        /* @var User $user */
        $user = auth()->user();
        return response($user->transfers, 200);
    }

    public function show(Transfer $transfer)
    {
        if (Gate::denies('access-transfer', $transfer)) {
            return response()->json([], 403);
        }
        return response($transfer, 200);
    }

    public function store(Account $account, StoreTransferRequest $request)
    {

        if (Gate::denies('access-account', $account)) {
            return response()->json([], 403);
        }

        if($account->bank == Bank::AYANDE
            && Carbon::now()->between(
                Carbon::createFromTimeString('23:53:59'),
                Carbon::createFromTimeString('23:59:59')
            )
        ){
            return response()->json(['message' => 'Can not Transfer at This time'], 500);
        }


        $transfer = resolve(Transfer::class);
        try {
            $transfer->createTransfer($account, $request->validated());

            return response([
                'Response' => 'Ok',
                'Message' => 'Transfer done Successfully',
                'pay-cycle' => $transfer->getPayaCycleEndPoint()
            ], 201);
        } catch (Exception $e) {
            return response([
                'error' => $e->getMessage()
            ], 500);
        }


    }
}
