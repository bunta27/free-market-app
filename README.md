# アプリケーション名
フリマアプリ

## プロジェクト概要
ユーザーが商品を出品・購入できるフリーマーケットアプリです。
会員登録・ログイン、プロフィール編集、商品検索、いいね機能、コメント機能、購入処理、マイページ（出品した商品・購入した商品一覧）などを実装しています。

---

## 環境構築
1. リポジトリ取得

  - git@github.com:bunta27/free-market-app.git (https://github.com/bunta27/free-market-app.git)
  - cd coachtech/laravel/free-market-app

2. .env 作成

  - cp .env.example .env

3. .env を docker-compose のサービス名に合わせて調整

  - DB_CONNECTION=mysql
  - DB_HOST=mysql
  - DB_PORT=3306
  - DB_DATABASE=laravel_db
  - DB_USERNAME=laravel_user
  - DB_PASSWORD=laravel_pass

4. コンテナ起動（ビルド）

  - docker-compose up -d --build

5. PHP コンテナに入って依存関係をインストール

  - docker-compose exec php bash
  - composer install

6. アプリケーションキーを生成

  - php artisan key:generate

7. マイグレーション & シーディング

  - php artisan migrate --seed

    MySQL が起動しない場合は OS によって設定が必要になることがあります。  
    各自の PC に合わせて `docker-compose.yml` の設定を調整してください。

## 使用技術（実行環境）

  - PHP 8.1.33  
  - Laravel Framework 8.83.8  
  - mysql 8.0.26  
  - Nginx 1.21.1  
  - Docker 28.3.2/ Docker Compose v2.39.1

## ER 図

<img src="docs/ER.svg" alt="ER図" width="1200">

## URL

  - 開発環境: http://localhost/  
  - phpMyAdmin: http://localhost:8080/

## ログイン情報
■ 一般ユーザー  
Email: demo@example.com  
Password: password

## 追加機能（応用実装）

### メール認証機能

- 新規会員登録時にメール認証用のメールを送信
- 初回ログイン時もメール認証が完了していない場合は、認証画面へリダイレクト
- 開発環境では MailHog を用いてメール内容を確認

#### メール送信設定（一例：MailHog 使用時）

```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="example@example.com"
MAIL_FROM_NAME="Free Market App"

### 決済機能（Stripe）

- 「コンビニ支払い」「カード支払い」を選択して「購入する」ボタンを押下すると、Stripe の決済画面に遷移
- 決済成功時に購入処理が行われ、購入情報が `sold_items` テーブルに保存される
- キャンセル時はキャンセル用画面（または詳細ページ）に遷移  
  （※ 実際の API キーなどは `.env` に設定してください）

### マイページ・その他機能

- マイページから以下を確認可能
  - 出品した商品一覧
  - 購入した商品一覧
- プロフィール編集機能（氏名・郵便番号・住所・建物名）
- いいね機能・コメント機能・商品検索機能を実装