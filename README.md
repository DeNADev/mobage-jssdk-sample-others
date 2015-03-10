mobage-jssdk-sample-others
===========================
## 動作確認環境

* Amazon Linux AMI 2014.03
* PHP 5.4.29
* MySQL 5.5.38
* [jwt](https://github.com/F21/jwt) ライブラリ（PHP 5.4.8 以上必要）

※ JSSDKではDOM-based XSSに対するセキュリティ上の対策としてContent Security Policyを導入しており、それによってinlineなJavaScriptの実行を禁止しています。Chromeのextensionの中にはこちらに抵触する処理を行うものがあるため、PCなどのChromeブラウザにて試す際には、Secret Modeで実行してください

## このサンプルの目的
Mobage JS SDK を利用した様々な処理をイメージしやすいように実装したサンプルです。

このサンプルは以下のような処理に関するサンプルで構成されています。
* ショートカットアイコン作成支援機能
* 友達招待機能

### 関連ドキュメント
こちらではサンプルを動かす簡単な手順をまとめていますが、Mobage JS SDK の詳細については「[Mobage JS SDK 開発ドキュメント](https://docs.mobage.com/display/JPJSSDK/Guide_for_Engineers)」を参照してください。

## サンプルを動かすまでの手順
### サンプルをインストールする
このサンプルは PHP のファイル群から構成されています。PHP が実行されるディレクトリにコピーします。

### ログインサンプルで Mobage Connect にログインする
ログインサンプル「mobage-jssdk-sample-login」を先にインストールし、ログインできる状態にしておきます。

また、デベロッパーサイトでの登録もログインサンプルの設定として行っておきます。

### config.phpを編集する
#### BASE_URIについて
BASE_URIは必ず
http://localhost
のように通信方式とホスト名のみで記載しましょう。

もしデフォルトディレクトリ以外にサンプルコードを配置した場合、適宜変更しましょう。

#### CLIENT_ID/CLIENT_SECRETについて
Mobage Developers Japanで発行されたClient_ID, Client_Secretを記載しましょう。

* SPWEB_CLIENT_ID / SPWEB_CLIENT_SECRET
 * スマートフォンブラウザ環境での CLIENT_ID / CLIENT_SECRET です。
* ANDROID_CLIENT_ID / ANDROID_CLIENT_SECRET
 * Shell App SDK for Android 環境での CLIENT_ID / CLIENT_SECRET です。
* IOS_CLIENT_ID / IOS_CLIENT_SECRET
 * Shell App SDK for iOS 環境での CLIENT_ID / CLIENT_SECRET です。


#### URI設定について
CLIENT_URI, REDIRECT_POST_ENDPOINT, REDIRECT_GET_ENDPOINTなどの値は、
Mobage Developers Japanであらかじめ登録した値を記載しましょう。
もしデフォルトディレクトリ以外にサンプルコードを置いた場合、こちらは適宜変更してください。


###PHP, MySQLがインストールされた環境を用意する
以下がインストールされた環境を用意しましょう。
 * PHP 5.4.29
 * MySQL 5.5.28

※サンプルコードの推奨実行環境です


### JWTライブラリを準備する
公開鍵 X509 formatで以下のライブラリを利用しています。  

https://github.com/F21/jwt

こちらのライブラリをダウンロードして以下のように配置しましょう。 
なお、こちらのライブラリを動作させるためには PHP 5.4.8 以上が必要です。

```
mobage-jssdk-sample-others/JWT/JWT.php
```

## 動作確認 
それぞれの機能について動作確認します。
事前にSandbox環境でのアカウント作成を行っておきましょう。
### ショートカットアイコン作成支援機能を確認する
#### デベロッパーサイトでアイテム情報の登録
以下に従って、デベロッパーサイトにショートカットアイコンを登録します。
* ダッシュボードの左メニューより「アプリケーション」>「アプリケーション一覧」画面を開きます。
* 「アプリケーション一覧」から開発しているアプリケーションを選択します。
* ダッシュボードの左メニューより「SPWeb設定」を選択します。
* 画面中央の「Mobage Connect 情報」にて「情報を変更」を押下します。
* 「Mobage Connect 情報編集」のメニューから「画像設定」を選択します。
* 「Sandbox用」を押下し、以下を設定します。
 * ショートカット起動時通知先URL(ショートカット起動時に、Platform から通知が送られるURLです)
 * ショートカットアイコンタイトル、起動時URL、画像
* 上記設定が完了したら、ステータスを「有効」としてください

#### サンプルコードを配置して実行する
サンプルコードを実機から確認できる環境に配置し、以下の手順で確認します。
* http://localhost/mobage-jssdk-sample-others/shortcut_icon/show_guide.php にアクセスします。
* ショートカットアイコンのガイドを表示する「Show Guide of Shortcut Icon」を押下します。
* ガイドに従って、ショートカットアイコンを作成します。
* 上記「ショートカットアイコン起動時通知先URL」に記入したURLに通知を受けるサンプルコードを配置しておきます。
* Server 側で Platform からの通知を監視しておきます。
 * サンプルコードでは、mobage-jssdk-others/log/shortcut_icon.log にて通知ログを出力しています。
 * 通知ログを 'tail -f' などのコマンドにより監視してください。
* 作成したショートカットアイコンを利用して、ゲームを起動します。
* Server 側で監視しておいた Platform からの通知を確認します。
 * 前述した通知ログを 'tail -f shortcut_icon.log' などで監視し、通知を確認します。

### 友達招待機能を確認する
#### デベロッパーサイトで通知先URLの登録
以下に従って、デベロッパーサイトに友達招待送信時、成立時に通知が来るURLを設定します。
* ダッシュボードの左メニューより「アプリケーション」>「アプリケーション一覧」画面を開きます。
* 「アプリケーション一覧」から開発しているアプリケーションを選択します。
* ダッシュボードの左メニューより「SPWeb設定」を選択します。
* 画面中央の「Mobage Connect 情報」にて「情報を変更」を押下します。
* 「Mobage Connect 情報編集」のメニューから「Invitation送信通知処理設定」を選択します。
* 「Subscricer Callback URI」へ、友達招待送信時に通知が送られるURIを設定します。
 * このサンプルコードでは「http://{yourdomain}/mobage-jssdk-sample-others/invitation/sent_async_invitation.php」と登録します。
* 「Mobage Connect 情報編集」のメニューから「Invitation成立通知処理設定」を選択します。
* 「Subscricer Callback URI」へ、友達招待成立時に通知が送られるURIを設定します。
 * このサンプルコードでは「http://{yourdomain}/mobage-jssdk-sample-others/invitation/accepted_async_invitation.php」と登録します。

#### サンプルコードを配置して実行する
サンプルコードを実機から確認できる環境に配置し、以下の手順で確認します。
* 友達招待を送信するアカウントを用意し、その招待者、被招待者をモバ友にしておきます。
 * 招待者は Game をインストールしていて、非招待者が Game をインストールをしていない状況をつくります。
* 招待送信の通知を確認する為のログを監視しておきます。
* http://localhost/mobage-jssdk-sample-others/invitation/invite.php にアクセスします。
* 友達招待サービスを表示する「Invite Friends」を押下します。
* 呼び出された画面から「モバ友を招待する」を選択します。
* 招待する人を選択し、「選択した人を招待」を押下します。
* Game Server 側でClient, Platform の双方からの友達招待送信通知を監視しておきます。
 * サンプルコードでは、mobage-jssdk-others/log/sent_invitation.log にて通知ログを出力しています
 * mobage-jssdk-others/log/sent_invitation.logの書き込み権限に注意してください。
 * 通知ログを 'tail -f sent_invitation.log' などのコマンドにより監視し、通知を確認します。
* 被招待者のMobage Chatに来ている招待メッセージから、被招待者にGameをInstallしてもらいます
 * 必ず招待メッセージからInstallするようにしてください。そうでないと招待成立となりません。
* Game Server 側でPlatformからの友達招待成立通知を監視しておきます。
 * サンプルコードでは、mobage-jssdk-others/log/accepted_invitation.log にて通知ログを出力しています
 * 通知ログを 'tail -f accepted_invitation.log' などのコマンドにより監視し、通知を確認します。
上記手順により、友達招待の送信通知、成立通知の受け取りまで確認できました。
