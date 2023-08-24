<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
     /**
     * add
     *
     * @param  mixed $request
     * @return void
     */
    public function add(Request $request)
    {
        try {

            $validation = Validator::make($request->all(), 
            [
                'amount' =>  'required|numeric|min:3|max:100'
            ]);

            if($validation->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validation->errors()
                ], 400);
            }

            $user = $request->user();
            $user->wallet = $user->wallet + floatval($request->amount);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Amount is successfully Added to Wallet',
                'data' => ['balance' => floatval(number_format($user->wallet,2)) ]
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * buycookie
     *
     * @param  Request $request
     * @return Response 
     */
    public function buycookie(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), 
            [
                'quantity' => 'required|numeric|min:1|gt:0'
            ]);


            if($validation->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validation->errors()
                ], 400);
            }

            $user = $request->user();
            $amount = floatval(round($request->quantity) * 1);

            if ($amount > $user->wallet) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient Balance in Wallet',
                    'errors' => 'Insufficient Balance'
                ], 400);
            }

            $user->wallet = $user->wallet - floatval($amount);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Payment succesfully completed',
                'data' => ['balance' => floatval(number_format($user->wallet,2)) ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
