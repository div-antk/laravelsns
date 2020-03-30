<?php

namespace App\Notifications;

use App\Mail\BareMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    // PasswordResetNotificationクラスに$token、$mailプロパティを定義
    public $token;
    public $mail;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    // public function __construct(string $token, BareMail $mail)
    public function __construct(string $token, BareMail $mail)
    {
        // コンストラクタメソッドにて、文字列である引数$token
        // BareMailクラスのインスタンスである引数$mailを上記のプロパティに代入
        $this->token = $token;
        $this->mail = $mail;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */

    // メールの具体的な設定
    public function toMail($notifiable)
    {
        return $this->mail
            // 第一引数は送信元アドレス、第二引数には送信者名（省略可）
            // config関数でconfig/mailに書かれた環境変数（env）を持ってくる
            // 送信者名はmemo、メアドはno-reply@example.com
            ->from(config('mail.from.address'), config('mail.from.name'))
            // 送信先ユーザーのメールアドレスを取得
            ->to($notifiable->email)
            ->subject('[memo]パスワード再設定')
            // テキスト形式のメールを送る場合に使うメソッド
            // 引数でメールのテンプレートを使用する
            ->text('emails.password_reset')
            // テンプレートのBladeに渡す変数をwithメソッドに連想配列形式で渡す
            ->with([
                // route関数を使ってpassword.resetのルーティングをセットする
                'url' => route('password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->email,
                ]),
                // パスワード設定画面へのURLの有効時間（分）がセットされる
                'count' => config(
                    'auth.passwords.' .
                    config('auth.defaults.passwords') .
                    '.expire'
                ),
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
