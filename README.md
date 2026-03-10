# アプリケーション名
フリマアプリ

## プロジェクト概要
ユーザーが商品を出品・購入できるフリーマーケットアプリです。
会員登録・ログイン、プロフィール編集、商品検索、いいね機能、コメント機能、購入処理、マイページ（出品した商品・購入した商品一覧）などを実装しています。

---

## 環境構築

### Docker ビルド

```bash
git clone git@github.com:bunta27/free-market-app.git
  
cd free-market-app
```

### Laravel セットアップ

#### ホスト側(コンテナ外)

```bash
cp .env.example .env
```

- .envを編集（例）

※ DB設定は必須です。MailHog を使わない場合は `MAIL_*` は環境に合わせて変更/削除してください。
  
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_FROM_ADDRESS="example@example.com"
MAIL_FROM_NAME="Free Market App"
```

```bash
docker compose up -d --build
```

### コンテナ側（Dockerの中）
```bash
docker compose exec php bash -lc "composer install"
docker compose exec php bash -lc "php artisan key:generate"
docker compose exec php bash -lc "php artisan migrate --seed"
docker compose exec php bash -lc "php artisan test"
```

---

## 動作確認

セットアップ完了後、http://localhost/ にアクセスしてログイン画面が表示されればOKです。  
メール認証の動作確認は MailHog (http://localhost:8025/) で受信できればOKです。

### URL

- 開発環境: http://localhost/  
- phpMyAdmin: http://localhost:8080/
- MailHog: http://localhost:8025/ （開発用メール受信確認）

## ログイン情報
■ 一般ユーザー  
Email: demo@example.com  
Password: password

---

## トラブルシューティング

### `storage/logs/laravel.log` や `storage/framework/sessions` で Permission denied が出る場合

```bash
docker compose exec php bash -lc "
mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache &&
chown -R www-data:www-data storage bootstrap/cache &&
chmod -R ug+rwX storage bootstrap/cache
"
```

### MySQL が起動しない場合（例: ポート3306競合 / volume不整合）
まずログを確認してください：

```bash
docker compose logs mysql
```
- ローカルで 3306 を使っているサービスがないか

---

## 使用技術（実行環境）

  - PHP 8.1.33  
  - Laravel Framework 8.83.8  
  - mysql 8.0.26  
  - Nginx 1.21.1  
  - Docker 28.3.2/ Docker Compose v2.39.1

## ER 図

<img src="docs/ER.svg" alt="ER図" width="1200">

---

## 追加機能（応用実装）

### メール認証機能

- 新規会員登録時にメール認証用のメールを送信
- 初回ログイン時もメール認証が完了していない場合は、認証画面へリダイレクト
- 開発環境では MailHog を用いてメール内容を確認

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