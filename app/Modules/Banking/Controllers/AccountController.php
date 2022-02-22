<?php

namespace App\Modules\Banking\Controllers;

use App\Models\User;
use App\Modules\Banking\Models\Account;
use App\Modules\Banking\Requests\StoreAccountRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


class AccountController extends BaseController
{
    public function index()
    {
        /* @var User $user */
        $user = auth()->user();
        return response($user->accounts, 200);
    }

    public function show(Account $account)
    {

        if (Gate::denies('access-account', $account)) {
            return response()->json([], 403);
        }

        return response($account);
    }

    public function store(StoreAccountRequest $request)
    {
        /* @var User $user */
        $user = auth()->user();
        $user->accounts()->create($request->validated());

        return response([
            'Response' => 'Ok',
            'Message' => 'Created Successfully',
        ], 201);
    }

    public function update(Request $request, Account $account)
    {
        if (Gate::denies('access-account', $account)) {
            return response()->json([], 403);
        }

        $attr = $request->validate([
            'description' => 'required',
        ]);

        $account->description = $attr['description'];
        $account->save();

        return response([
            'Response' => 'Ok',
            'Message' => 'Updated Successfully',
        ], 200);
    }


    public function destroy(Account $account)
    {
        if (Gate::denies('access-account', $account)) {
            return response()->json([], 403);
        }

        $account->delete();

        return response([
            'Response' => 'Ok',
            'Message' => 'Deleted Successfully',
        ], 200);
    }

    public function addCredit(Account $account, Request $request)
    {

        if (Gate::denies('access-account', $account)) {
            return response()->json([], 403);
        }

        $attr = $request->validate([
            'credit' => 'required|numeric|gt:0',
        ]);

        $account->addCredit($attr['credit']);

        return response([
            'Response' => 'Ok',
            'Message' => 'Updated Successfully',
        ], 200);
    }


}
