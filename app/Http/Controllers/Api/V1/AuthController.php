<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="API وكالات السفر",
 *     version="1.0.0",
 *     description="واجهة برمجة التطبيقات لنظام وكالات السفر",
 *     @OA\Contact(
 *         email="support@example.com",
 *         name="فريق الدعم"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * @OA\Server(
 *     url="/api/v1",
 *     description="خادم API الرئيسي"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="تسجيل الدخول للحصول على رمز الوصول",
     *     tags={"المصادقة"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تسجيل دخول ناجح",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="بيانات اعتماد غير صالحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="بيانات الاعتماد غير صالحة")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'بيانات الاعتماد غير صالحة'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }
}
