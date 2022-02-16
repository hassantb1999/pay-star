<?php

namespace App\Modules\Banking\Controllers;

use App\Models\User;
use App\Modules\Banking\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


class AccountController extends BaseController
{
    public function index(){
//        return auth()->user()->accounts();
        return Account::all();
    }

    public function show($id){


        $account = Account::find($id);
        return $account ? response($account) : response(null);
    }

    public function store(Request $request) {

        $account = new Account();
        $account->createAccount(
            $request->get('owner_id'),
            $request->get('bank'),
            $request->get('account_number'),
            $request->get('shaba_number'),
            $request->get('credit'),
            $request->get('description'),
        );

        return response([
            'Response' => 'Ok',
            'Message' => 'Created Successfully',
        ], 201);
    }

    public function update(Request $request,int $id) {
        $account = Account::find($id);

        /* Validate description */

        $account->description = $request->get('description');
        $account->save();

        return response([
            'Response' => 'Ok',
            'Message' => 'Updated Successfully',
        ], 200);
    }


    public function destroy(int $id)
    {
        $account = Account::find($id);

        $account->delete();
        return response([
            'Response' => 'Ok',
            'Message' => 'Deleted Successfully',
        ], 200);

    }
}
