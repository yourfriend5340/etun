<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Customer;

class CustomerAuthController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'account' => 'required',
        'password' => 'required',
    ]);

    $credentials = [
        'account' => $request->account,
        'password' => $request->password,
    ];

    if (!Auth::guard('customer')->attempt($credentials)) {
        return response()->json(['message' => 'login failed'], 401);
    }

    $user = Auth::guard('customer')->user();

    $token = $user->createToken('API Token')->accessToken;

    return response()->json([
        'name' => $user->firstname,
        'account' =>$user->account,
        'token' => $token,

    ]);
}


}
