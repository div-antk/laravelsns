<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // 外部サービスのログイン用
        // driverにサービス名を渡し、redirectでそのサービス画面へリダイレクトさせる
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider)
    {
        // Laravel\Socialite\Two\Userというクラスのインスタンスを取得する
        // Laravel\Socialite\Two\Userクラスのインスタンスでは、Googleから取得したユーザー情報をプロパティとして持っている
        $providerUser = Socialite::driver($provider)->stateless()->user();

        // Googleから取得したユーザー情報からメールアドレスを取得できる
            // メールアドレスをwhereメソッドの第二引数に渡し、条件に一致するユーザーモデルをコレクションとして取得し
                // コレクションの最初の1件のユーザーモデルを取得する
        $user = User::where('email', $providerUser->getEmail())->first();

        // Googleから取得したメールアドレスと同じものを持つユーザーがいない場合、$user  にはnullが入る
        if ($user) {
            // ユーザーをログイン状態にする
            $this->guard()->login($user, true);
            return $this->sendLoginResponse($request);
        }
            // 早期return。ifの条件に該当しなかったとき
            return redirect()->route('register.{provider}', [
                // プロバイダー名（Google）
                'provider' => $provider,
                // Googleから取得したメールアドレス
                'email' => $providerUser->getEmail(),
                // Googleから発行されたトークンが返る
                'token' => $providerUser->token,
            ]);
    }
}
