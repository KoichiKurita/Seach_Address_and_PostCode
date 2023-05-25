<?php declare(strict_types=1); ?>

<!--
    Webアプリ実践課題
    郵便番号検索(result_address.php)
    作成者：リンクス新越谷 栗田幸一
    作成日：2023.3.31
    修正日：2023.4.8
-->

<!-- メインルーチン -->
<!-- 郵便番号フォームの入力内容から住所を検索する -->
<?php
    require_once dirname(__FILE__). '/functions.php';   // 外部関数読み込み
    require_once dirname(__FILE__). '/config.php';      // 外部関数読み込み

    // 入力された郵便番号
    $post_code = $_GET['post-code'];

    // ハイフンをUnicode「U+002D(-)」に変換する
    if (preg_match(PATTERN_HYPHEN, $post_code) === 1)  {
        $post_code = preg_replace(PATTERN_HYPHEN, '-', $post_code);
    }

    // DBに接続し、郵便番号を検索する。
    try {

        $pdo = connect();   // DB接続

        // 郵便番号から住所を検索する
        $statement = search_address($pdo, $post_code);

    } catch (PDOException $e) {
        echo '住所の検索に失敗しました。';
        echo 'エラー理由'. $e->getMessage(). PHP_EOL;
        return;
    }
?>

<!-- 以下、結果表示用Webページ -->
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>住所検索結果</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <!-- result.phpにも検索フォームを表示する -->
        <h1>住所検索</h1>
        <p id="direction">郵便番号を入力してください。郵便番号検索は<a href="search_postcode.php">こちら</a></p>
        <p class="note">注：7桁の数字で入力してください。ハイフン（-）は有り・無しどちらでも検索可能です。</p>
        <form name="search-address" action="result_address.php" method="GET">
            <div class="post-code-input-wrapper">
                <div class="post-code-input">
                    <label for="post-code">郵便番号<span class="required">（必須）</span>：
                        <input type="text" name="post-code" id="post-code">
                        <!-- 郵便番号の書式で入力させる -->
                    </label>
                </div>
            </div>

            <button type="submit" name="operation" value="search" id="button">検索開始</button>
        </form>

        <!-- 入力された郵便番号の書式が誤っている場合にエラーメッセージを表示する部分 -->
        <p id="error-message"></p>

        <hr size="5" color="black" noshade>     <!--- 罫線 --->

        <!-- フォームに入力された郵便番号とデータベースで検索した結果を表形式で表示する -->
        <h1 id="result_heading">郵便番号「<?=escape($_GET['post-code'])?>」の住所の検索結果：<?=$statement->rowCount()?>件</h1>
        <table border="1">
            <tr>
                <th>郵便番号</th>
                <th>住所</th>
            </tr>

            <?php while ($row = $statement->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?=escape($row['post_code'])?></td>
                <td><?=escape($row['prefecture']. $row['municipality']. $row['town_area'])?></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <a href='search_address.html'>住所検索トップへ戻る</a>

        <!-- JavaScript -->
        <!-- Javascript直下にコメント入れるとページロード完了後に動作しないことがあるので注意する -->
        <script src="js/result_address_script.js" type="module"></script>
    </body>
</html>