<?php

// ユーザー登録用

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // バリデーション
        return Validator::make($data, [
            'name' => ['required', 'string', 'alpha_num', 'min:3', 'max:16', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function showProviderUserRegistrationForm(Request $request, string $provider)
    {
        $token = $request->token;

        // useFromTokenメソッドで、Googleから発行済のトークンを使い
        //     GoogleのAPIに再度ユーザー情報の問い合わせを行い、取得したユーザー情報を代入
        $providerUser = Socialite::driver($provider)->userFromToken($token);

        return view('auth.social_register', [
            // プロバイダー名（Google）
            'provider' => $provider,
            // Googleから取得したメールアドレス
            'email' => $providerUser->getEmail(),
            // Googleから発行されたトークンが返る
            'token' => $providerUser->token,
        ]);
    }

    public function registerProviderUser(Request $request, string $provider)
    {
        // 実際にユーザー登録に使用するメールアドレスはGoogleのAPIから再取得するものであるため、バリデーションは行わない
        $request->validate([
            'name' => ['required', 'string', 'alpha_num', 'min:3', 'max:16', 'unique:users'],
            'token' => ['required', 'string'],
        ]);

        // Googleから発行済のトークンの値を取得して代入
        $token = $request->token;

        // useFromTokenメソッドで、Googleから発行済のトークンを使い
        //     GoogleのAPIに再度ユーザー情報の問い合わせを行い、取得したユーザー情報を代入
        $providerUser = Socialite::driver($provider)->userFromToken($token);

        // ユーザーモデルのcreateメソッドを使い、ユーザーモデルのインスタンスを作成
        $user = User::create ([
            // 登録画面に入力されたユーザー名
            'name' => $request->name,
            // Googleから取得したメールアドレス
            'email' => $providerUser->getEmail(),
            // パスワードは登録不要
            'password' => null,
        ]);

        // ユーザー登録後にログイン済み状態にし、記事一覧画面にリダイレクト
        $this->guard()->login($user, true);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
}
