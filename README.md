# learn-php

## What's is this?
このリポジトリは「バックエンド入門としてのPHP」のために作成されました。

## How to start.
Windowsの方は`Command`を`Ctrl`に変換して実施してください。

1. `必要準備`に書かれているソフトウェアをダウンロード + インストール
2. Docker for Desktopを起動する。
3. VSCodeで本リポジトリを開く。
4. 環境構築を行う。
    1. VSCodeを起動し、開発環境を立ち上げる。  
      以下どれかを実行しdockerを起動する
        - VSCodeを選択している状態で `Command + Shift + B`
        - メニューの`ターミナル` > `タスクの実行` > `docker start`をクリック
        - VSCode内でターミナルを起動する。  
          (VSCode内でのターミナル起動方法: `Command + Shift + P`を押し、`create new terminal`と入力しエンター)
          - 手動でdockerコマンド`docker compose up -d`を入力しエンター

      -> いろいろダウンロードやビルドが実施されるのでしばらく待つ  
      -> いろいろ表示されていたのが静かになり doneと表示されるか、次のコマンドが入力できるようになったら完了！  

  5. ブラウザで以下にアクセスし、つながることを確認。  
        - WEBサーバ: http://localhost
        - APIサーバ: http://localhost:4001
        - phpmyadmin: http://localhost:8080

  **上記手順で困ったら:**  

|困ったこと|手順|
|--|--|
|Windowsにて、Dockerインストールしたところ「wsl 2 installation is incomplete」エラーが出た|[こちらの記事を参照](https://qiita.com/zembutsu/items/22a5cae1d13df0d04e7bs)し解決してください|
|dockerコマンド入力後、処理がとても長く、表示が止まったり進まなくなった|ターミナルを選択し`Ctrl+C`(Win/Mac共通)を何度か入力し処理を中断後、Docker for Desktopを再起動して再度コマンドを入力してください|

## 必要準備
下記ソフトウェアをダウンロード+インストールしてください。

### 必須級
- 環境構築に利用: [Docker for Desktop](https://www.docker.com/products/docker-desktop)
- 編集エディタ: [VSCode](https://code.visualstudio.com/download)
  - デバッグサポート拡張: [VSCode拡張 PHP Debug](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-debug)
- [Windowsの方のみ][Git Bash](https://gitforwindows.org/)  
  VSCodeのデフォルトターミナル変更は以下を参照
  - [VSCode のターミナルを Git Bash に変更](https://qiita.com/daikiozawa/items/48a9fe0e2898c7dd78ae)

### 推奨
- OpenAPIクライアントアプリ: [Stoplight](https://stoplight.io/studio/)
- APIを呼び出すChrome拡張機能: [Talend API Tester](https://chrome.google.com/webstore/detail/talend-api-tester-free-ed/aejoelaoggembcahagimdiliamlcdmfm?hl=ja)
- VSCodeが日本語でなくて辛い方へ: [Japanese Language Pack](https://marketplace.visualstudio.com/items?itemName=MS-CEINTL.vscode-language-pack-ja)
- VSCodeのphpフォーマッタ: [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client)

## ディレクトリ構成
```
.
├── README.md                   # 最初に読む文章
├── docker                      # dockerの設定ファイル
│   ├── mysql                   # DBのdcokerファイル
│   │   ├── Dockerfile          #
│   │   └── initial.sql         # dockerを最初に起動した際に実行されるSQL
│   ├── nginx                   # WEBサーバのdockerファイル
│   │   ├── Dockerfile          #
│   │   ├── conf.d              #
│   │   │   └── default.conf    #
│   │   └── log                 # WEBサーバのログファイル
│   │       ├── 4001_access.log # ポート4001のバックエンドのAPPサーバのアクセスログ
│   │       ├── 4001_error.log  # 　　　　　〃　　　　　　　　　　　　　　エラーログ
│   │       ├── access.log      # 静的ファイルなどを担当するWEBサーバのアクセスログ
│   │       └── error.log       # 　　　　　〃　　　　　　　　　　　　　エラーログ
│   └── php                     # 
│       ├── Dockerfile　　　　　　# PHPのAPPサーバのdockerファイル
│       ├── log                 #
│       │   └── xdebug.log      # phpのデバッガーxdebugのログファイル
│       └── php.ini             # phpの設定ファイル
├── docker-compose.yml          # 各dockerの連携を記載したdocker-composeファイル
├── reference                   #
│   └── whisperAPI.json         # APIの仕様を記載したOpenAPIファイル
└── src                         # 実際のプロダクトコードが以下に格納
    ├── backend                 # バックエンドのコード
    │   └── index.php           # 
    └── frontend                # フロントエンドのコード
        ├── css                 #
        │   ├── local.css       #
        │   └── materialize.min.css # 
        ├── index.html          #
        └── js                  #
            ├── local.js        #
            └── materialize.min.js #
```

## docker環境構成
WEB,APP,DB,phpmyadminの4つのコンテナが実行される。
(詳しくは docker-compose.ymlを参照)
- web: 静的ファイルの返却と、PHPリクエストの場合appサーバへのリダイレクトを行う。 `src/frontend`がアタッチしている
- app: PHPを処理するコンテナ。`src/backend`をアタッチしている。
- db: mysqlを実行しているコンテナ。DBのデータは永続化してある。
- phpmyadmin: DBへのアクセスや操作などがGUIからできる、phpmyadminを実行しているコンテナ。
