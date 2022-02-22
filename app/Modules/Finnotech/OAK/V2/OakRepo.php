<?php

namespace App\Modules\Finnotech\OAK\V2;

use App\Modules\Banking\Models\Account;
use App\Modules\Banking\Models\Bank;
use App\Modules\Banking\Models\Transaction;
use Carbon\Carbon;

/**
 *
 */
trait OakRepo
{


    public static function getTransferExpectedData(Account $account): array
    {
        $expected_data = [
            'amount' => null,
            'description' => null,
            'destinationFirstname' => null,
            'destinationLastname' => null,
            'destinationNumber' => null,
        ];

        return match ($account->bank) {
            Bank::PARSAIN => array_merge($expected_data, ['secondPassword' => null]),
            Bank::AYANDE => array_merge($expected_data, ['paymentNumber' => null]),
            Bank::KESHAVARZI => array_merge($expected_data, [
                'deposit' => $account->account_number,
                'sourceFirstName' => $account->owner->first_name,
                'sourceLastName' => $account->owner->last_name
            ]),
        };
    }

    public function fakeTransferToResponse($requestData, $trackId)
    {
        return [
            'result' => [
                'amount' => $requestData['amount'],
                'description' => $requestData['description'],
                'destinationFirstname' => $requestData['destinationFirstname'],
                'destinationLastname' => $requestData['destinationLastname'],
                'destinationNumber' => $requestData['destinationNumber'],
                'inquiryDate' => (string)rand(10000, 100000),
                'inquiryTime' => Carbon::now()->toString(),
                'inquirySequence' => 1001,
                'message' => " ",
                'paymentNumber' => (string)rand(10, 100000),
                'refCode' => (string)rand(1000000000000, 10000000000000),
                'type' => 'fake'

            ],
            'status' => Transaction::STATUS_DONE,
            'trackId' => $trackId
        ];
    }
}
