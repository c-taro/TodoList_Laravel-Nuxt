## インストール

```bash
# プロジェクトルート"TodoList_Laravel-Nuxt"に"docker-compose.yml"で使用する環境ファイルを用意
cp .env.example .env

# コンテナ立ち上げ
docker compose up -d

# appコンテナにライブラリをインストール
docker compose exec app bash
composer install

# appコンテナに環境ファイルを用意
cp .env.example .env
php artisan key:generate
```

ブラウザから `http://localhost:8080/` にアクセスすると画面に以下の文字列が表示される。
```
{"Laravel":"8.83.27"}
```

```bash
# appコンテナでマイグレーションを実行
php artisan migrate:fresh --seed
exit
```

```bash
# webコンテナでライブラリをインストール
docker compose exec web sh
yarn install

# webコンテナを起動
yarn dev
```

ブラウザから `http://localhost:3000/` にアクセスするとログイン画面が表示される。

以下のアクセス情報でログイン可能。

メールアドレス：
```
p@p.pp
```
パスワード：
```
pppppppp
```


## 自動テストの実行
```bash
# appコンテナにdb_testコンテナ(自動テスト実行環境)用の環境ファイルを用意
docker compose exec app bash
cp .env.example .env.testing
php artisan key:generate --env=testing
```

作成した環境ファイル(.env.testing)をdb_testコンテナ用に修正
```
# .env.testingの修正例
...
DB_CONNECTION=mysql
DB_HOST=db_test
DB_PORT=3306
DB_DATABASE=test_database
DB_USERNAME=root
DB_PASSWORD=password
...
```

```bash
# appコンテナでdb_testデータベースに対しマイグレーションを実行
php artisan migrate --env=testing

# appコンテナから自動テストを実行
php artisan test
exit
```