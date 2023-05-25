/*
    Webアプリ実践課題
    住所検索(result_address_script.js)
    作成者：リンクス新越谷 栗田幸一
    作成日：2023.3.31
    修正日：2023.5.25
 */

// インポート
import {getUrlQueryData,
        setSelectorTopOption,
        setSelectorOptions,
        removeSelectorOption
    } from "./functions.js"; // 外部関数
import {PATH_GET_MUNICIPALITY_PROCESS, PATTERN_HYPHEN} from "./config.js"; // ハイフンのパターン文字列

// Webページロード時にJavaScriptを実行する
document.addEventListener('DOMContentLoaded', function() {

    // 郵便番号のselect要素を取得
    const post_code = document.getElementById('post-code');

    // URLからクエリ文字列を取得する
    const query_data = getUrlQueryData();

    // 検索フォームで入力した町名を残す
    post_code.value = query_data['post-code'];

    // ハイフンをUnicode「U+002D(-)」に変換する
    if(PATTERN_HYPHEN.test(post_code.value)) {
        post_code.value = post_code.value.replace(PATTERN_HYPHEN, '-');
    }

    document.getElementById('button').addEventListener('click', function(event) {
        let pattern = /[0-9]{3}-?[0-9]{4}/u;    // 郵便番号を検査するパターン文字列
        let post_code = document.getElementById('post-code').value;     // 入力された郵便番号

        // ハイフンをUnicode「U+002D(-)」に変換する
        if(PATTERN_HYPHEN.test(post_code.value)) {
            post_code.value = post_code.value.replace(PATTERN_HYPHEN, '-');
        }

        // 入力された郵便番号の書式チェック
        if (!(pattern.test(post_code))) {   // 郵便番号の形式でない文字列が入力された場合
            document.getElementById('error-message').textContent = '7桁の数字で入力してください';
            event.preventDefault();         // 結果表示ページ(result_address.php)に遷移しない
        }
    }, false);
}, false);