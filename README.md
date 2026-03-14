# アプリケーション名
フリマアプリ

## プロジェクト概要
ユーザーが商品を出品・購入できるフリーマーケットアプリです。
会員登録・ログイン、プロフィール編集、商品検索、いいね機能、コメント機能、購入処理、マイページ（出品した商品・購入した商品一覧）などの基本機能に加え、購入後のやり取りを行う取引チャット機能と、取引完了後の購入後評価機能を追加実装しています。

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
cp src/.env.example src/.env
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
※ `php artisan migrate:fresh --seed` は、DBを初期化してダミーデータを再投入します。

```bash
docker compose exec php bash -lc "cd /var/www && composer install"
docker compose exec php bash -lc "cd /var/www && php artisan key:generate"
docker compose exec php bash -lc "cd /var/www && php artisan migrate:fresh --seed"
docker compose exec php bash -lc "cd /var/www && php artisan storage:link"
docker compose exec php bash -lc "cd /var/www && php artisan test"
```

---

## 動作確認

セットアップ完了後、http://localhost/ にアクセスしてトップページ（商品一覧）が表示されればOKです。
メール認証の動作確認は MailHog (http://localhost:8025/) で受信できればOKです。

### URL

- 開発環境: http://localhost/  
- phpMyAdmin: http://localhost:8080/
- MailHog: http://localhost:8025/ （開発用メール受信確認）

## ログイン情報
### テストユーザー
- 出品者1  
  email: seller1@example.com  
  password: password

- 出品者2  
  email: seller2@example.com  
  password: password

- 未使用ユーザー  
  email: user3@example.com  
  password: password

### 商品データ
- C001〜C005 は出品者1が出品  
- C006〜C010 は出品者2が出品

---

## 追加実装確認用データ
- 出品者1では、取引中データを3件確認できます（未読メッセージあり2件、既読済み1件）
- 取引中の商品タブから取引画面へ遷移し、未読メッセージ件数のバッジ表示を確認できます
- 出品者2では、完了済み取引データを確認できます
- 完了済み取引には相互評価データを投入しているため、マイページで評価平均を確認できます
- 各取引にはサンプルの取引チャットメッセージを投入しています

---

## 注意事項
- Laravel アプリケーション本体は src ディレクトリにあります。
- 商品画像を表示する場合は、src/storage/app/public/items/ に画像ファイルを配置してください。
- 商品画像を表示するには、Seeder に設定しているファイル名と実際の画像ファイル名を一致させる必要があります。
- Stripe を利用する場合は、別途 .env に API キーの設定が必要です。

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
  - PHPUnit 9.6.34

## ER 図

<img src="docs/ER.svg" alt="ER図" width="1500">

---

## 追加機能（応用実装）

### メール認証機能

- 新規会員登録時にメール認証用のメールを送信
- 初回ログイン時もメール認証が完了していない場合は、認証画面へリダイレクト
- 認証画面から認証メールの再送信が可能です
- 開発環境では MailHog を用いてメール内容を確認

### 決済機能（Stripe）

- 「コンビニ支払い」「カード支払い」を選択して「購入する」ボタンを押下すると、Stripe の決済画面に遷移します
- 決済成功時に購入処理が行われ、購入情報が `sold_items` テーブルに保存されます
- キャンセル時はキャンセル用画面（または詳細ページ）に遷移します  
- 開発環境（local / testing）では追加機能の動作確認をしやすくするため、実際の Stripe 決済画面には遷移せず、疑似的に購入処理を完了する仕様にしています
- 実際に Stripe を利用する場合は `.env` にAPIキーの設定が必要です

### マイページ・その他機能

- マイページから以下を確認可能
  - 出品した商品一覧
  - 購入した商品一覧
  - 取引中の商品一覧
  - ユーザー評価平均表示
- プロフィール編集機能（氏名・郵便番号・住所・建物名）
- いいね機能・コメント機能・商品検索機能を実装

### 取引チャット機能
- 商品購入後に取引データが生成され、出品者と購入者の間で取引チャットを行うことができます
- 取引画面は当事者のみ閲覧可能です
- メッセージの投稿機能を実装しており、テキストおよび画像付きメッセージに対応しています
- 自分が投稿したメッセージのみ編集・削除が可能です
- マイページの「取引中の商品」タブから各取引画面へ遷移できます
- 未読メッセージ件数を商品画像左上のバッジで確認できます
- 未読メッセージがある取引を優先して表示し、取引画面を開くと既読化されます

### 購入後評価機能
- 購入者が取引完了ボタンを押すことで、取引を完了状態にできます
- 取引完了時に、出品者へ通知メールを自動送信します
- 取引完了後、出品者・購入者の双方が相手を5段階で評価できます
- 評価は1取引につき1人1回のみ可能です
- 評価結果はマイページ上に平均値として反映されます

---

## テスト
- Featureテストを追加し、取引チャット機能および購入後評価機能の動作確認を行っています
- 取引画面の閲覧権限、メッセージ送信 / 編集 / 削除権限、購入者のみ取引完了できることを検証しています
- 評価機能については、取引完了後のみ評価可能であること、バリデーションが正しく動作することを検証しています
- マイページの「取引中の商品」タブ表示、未読件数バッジ表示についても確認しています
- テストは php artisan test で実行できます