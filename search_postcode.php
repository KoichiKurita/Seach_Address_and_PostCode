<?php declare(strict_types=1); ?>

<!--
    Webアプリ実践課題
    郵便番号検索(search_postcode.php)
    作成者：リンクス新越谷 栗田幸一
    作成日：2023.4.7
-->

<!-- メインルーチン -->
<!-- 住所フォームの入力内容から郵便番号を検索する -->
<?php
    require_once dirname(__FILE__). '/functions.php';   // 外部関数読み込み

    // DBに接続し、都道府県データを検索する。
    try {

        $pdo = connect();   // DB接続

        // DBから都道府県一覧データを取得する
        $statement = get_prefecture($pdo);

    } catch (PDOException $e) {
        echo '都道府県データの取得に失敗しました。';
        echo 'エラー理由'. $e->getMessage(). PHP_EOL;
        return;
    }
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>郵便番号検索</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <h1>郵便番号検索</h1>
        <p id="direction">都道府県, 市区町村, 町名を入力してください。住所検索は<a href="search_address.html">こちら</a></p>
        <form name="search-post-code" action="result_postcode.php" method="GET">
            <div class="address-input-wrapper">
                <div class="address-input">
                    <label for="prefecture">都道府県<span class="required">（必須）</span>：
                        <select name="prefecture" id="prefecture" required>
                            <option value="">選択してください</option>
                            <?php while ($row = $statement->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?=escape($row['prefecture'])?>"><?=escape($row['prefecture'])?></option>
                            <?php endwhile; ?>
                        </select>
                    </label>
                </div>

                <div class="address-input">
                    <label for="municipality">市区町村<span class="required">（必須）</span>：
                        <select name="municipality" id="municipality" disabled required>
                            <option value="">選択してください</option>
                        </select>
                    </label>
                </div>

                <div class="address-input">
                    <label for="town_area">町名：
                        <input type="text" name="town_area" id="town_area">
                    </label>
                </div>
            </div>

            <button type="submit" name="operation" value="search">検索開始</button>
        </form>

        <!-- JavaScript -->
        <!-- .js間のモジュール読み込みはサーバにアップロードした状態でないと実行できない。（ローカルではエラーになる。）
        また、.jsファイルを読み込む.jsファイルに対して、html側でtype="module"で読み込まないといけない。 -->
        <!-- Javascript直下にコメント入れるとページロード完了後に動作しないことがあるので注意する -->
        <script src="js/search_postcode_script.js" type="module"></script>
    </body>
</html>