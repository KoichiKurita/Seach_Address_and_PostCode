<?php
    declare(strict_types=1);

    /**
     * Webアプリ実践課題
     * 郵便番号・住所検索(functions.php)
     * 作成者：リンクス新越谷 栗田幸一
     * 作成日：2023.3.31
     * 修正日：2023.4.8
     */

    /**
     * PDOインスタンスを取得する関数
     * @return pdo PHP Data Object を返します。
     */
    function connect() :pdo
    {
        // $pdo = new PDO('mysql:host=localhost; dbname=links_prog_exercise; charset=utf8mb4', 'root', '');    // DB接続（ローカル環境）
        $pdo = new PDO('mysql:host=mysql1.php.xdomain.ne.jp; dbname=xd868156_db; charset=utf8', 'xd868156_1', 'vFnwS7a6PNr2PVhQ');    // DB接続（デプロイ環境）
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);  // SQLをプリコンパイルしてから実行。処理速度向上とSQLインジェクション対策
        return $pdo;
    }


    /**
     * 都道府県を郵便番号データベースから取得する関数
     * @param pdo $pdo データベースに接続した PHP Data Object
     * @return PDO::prepare SQL statement を返します
     */
    function get_prefecture(PDO $pdo) :PDOStatement
    {
        // 郵便番号データベースから都道府県の一覧データを取得
        $statement = $pdo->prepare('SELECT DISTINCT prefecture FROM post_codes');
        $statement->execute();

        return $statement;
    }


    /**
     * 市区町村を郵便番号データベースから取得する関数
     * @param pdo $pdo データベースに接続した PHP Data Object
     * @return PDO::prepare SQL statement を返します
     */
    function get_municipality(PDO $pdo, string $selected_prefecture) :PDOStatement
    {
        // 郵便番号データベースから市区町村の一覧データを取得
        $statement = $pdo->prepare('SELECT DISTINCT municipality FROM post_codes WHERE prefecture = :prefecture');
        $statement->bindValue(':prefecture', $selected_prefecture, PDO::PARAM_STR);
        $statement->execute();

        return $statement;
    }


    /**
     * 郵便番号をデータベースから検索する関数
     * @param pdo $pdo データベースに接続した PHP Data Object
     * @param string $prefecture 都道府県
     * @param string $municipality 市区町村
     * @param string $town_area=null 町名
     * @return PDO::prepare SQL statement を返します
     */
    function search_post_code(PDO $pdo, string $prefecture, string $municipality, string $town_area = null) :PDOStatement
    {
        // 町名（town_area）の有無でSQL文を変える
        if ($town_area === null) {  // 町名がない場合
            // SQL検索結果には結果表示に必要な列のみ取得する（動作の高速化）
            $statement = $pdo->prepare('SELECT post_code, prefecture, municipality, town_area FROM post_codes WHERE prefecture = :prefecture AND municipality = :municipality');
        } else {    // 町名がある場合
            $statement = $pdo->prepare('SELECT post_code, prefecture, municipality, town_area FROM post_codes WHERE prefecture = :prefecture AND municipality = :municipality AND town_area LIKE :town_area');
            $statement->bindValue(':town_area', '%'. $town_area. '%', PDO::PARAM_STR);
        }

        // SQL文のパラメータをバインド
        $statement->bindValue(':prefecture', $prefecture, PDO::PARAM_STR);
        $statement->bindValue(':municipality', $municipality, PDO::PARAM_STR);

        // 郵便番号検索実行（SQL文を実行）
        $statement->execute();

        return $statement;
    }


    /**
     * 郵便番号をデータベースから検索する関数
     * @param pdo $pdo データベースに接続した PHP Data Object
     * @param string $post_code 郵便番号
     * @return PDO::prepare SQL statement を返します
     */
    function search_address(PDO $pdo, string $post_code) :PDOStatement
    {
        // ハイフンの有無で検索処理を分ける
        if (preg_match('/^[0-9]{3}\-[0-9]{4}/u', $_GET['post-code']) > 0) { // ハイフンが付いている場合
            $statement = $pdo->prepare('SELECT post_code, prefecture, municipality, town_area FROM post_codes WHERE post_code = :post_code');
        } else {    // ハイフンがついていない場合
            $statement = $pdo->prepare('SELECT post_code, prefecture, municipality, town_area FROM post_codes WHERE post_code_noHyphen = :post_code');
        }

        // SQL文のパラメータをバインド
        $statement->bindValue(':post_code', $post_code, PDO::PARAM_STR);

        // 郵便番号検索実行（SQL文を実行）
        $statement->execute();

        return $statement;
    }


    /**
     * HTMLエスケープする関数
     * @param mixed $value
     * @return string エスケープ処理した文字列を返します。
     */
    function escape($value)
    {
        return htmlspecialchars(strval($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
?>