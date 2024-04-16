<?php
session_start();

//１．関数群の読み込み
include("funcs.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

//LOGINチェック → funcs.phpへ関数化しましょう！
sschk();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>POTZ チャットアプリ</title>
    <link href="css/chat.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</head>
<body>

<header class="header">
    <div><?= htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?>さん</div>
</header>

<div id="phone">
    <div id="screen">
        <!-- Output window for chat messages -->
        <div id="output" class="scroll_bar" style="overflow-y: auto; overflow-x: hidden; height: 600px;"></div>
        <audio id="mySound" src="sound/syupon01.mp3" preload="auto"></audio>

        <!-- Chat message submission form -->
        <form method="POST" action="chat_rw.php" enctype="multipart/form-data">
            <div class="send_wrap">
                <fieldset>

                    <label>投稿：<?= htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?></label><br>
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION["user_id"], ENT_QUOTES); ?>">
                    <input type="hidden" name="user_name" value="<?= htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?>">
                    <input type="text" name="content" class="chat_input">
                　　 <button id="send" type="submit"><img src="img/btn_send.png" alt="送信"></button>

                 </fieldset>
            </div>
            <div id="stamp-gallery" class="gallery"></div>
        </form>
    </div>
</div>



<script>
// メッセージを読み込む関数をグローバルスコープに移動
var currentUserId = <?= json_encode($_SESSION['user_id']); ?>; // PHPセッションからユーザーIDを取得

function loadMessages() {
    $.ajax({
        url: 'chat_rw.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const output = $('#output');
            output.empty(); // 古いメッセージをクリア

            if (data.length > 0) {
                $.each(data, function(i, message) {
                    var date = new Date(message.timestamp);
                    var formattedTime = (date.getMonth() + 1) + '/' + date.getDate() + ' ' +
                                        date.getHours() + ':' + 
                                        (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes());

                    var alignmentClass = (message.user_id == currentUserId) ? 'right' : 'left';
                    var userClass = (message.user_id == currentUserId) ? 'its_me' : 'not_me'; 
                    var timeAlignmentClass = (message.user_id == currentUserId) ? 'time-right' : 'time-left';
                    var messageHtml;

                    if (message.message_type === 'stamp') {
                        // スタンプの場合
                        messageHtml = '<div class="message-wrapper ' + alignmentClass + '">' + 
                          '<div class="user-name">♡: ' + $('<div/>').text(message.user_name).html() + '</div>' +
                          '<img src="' + message.content + '" style="max-width: 380px;">' +
                          '<div class="message-time ' + timeAlignmentClass + '">' + formattedTime + '</div>' +
                          '</div>';
                     } else {
            // テキストの場合
                         messageHtml = '<div class="message-wrapper ' + alignmentClass + '">' + 
                          '<div class="user-name">♡: ' + $('<div/>').text(message.user_name).html() + '</div>' +
                          '<div class="message-content ' + userClass + '">' + $('<div/>').text(message.content).html() + '</div>' + // userClass
                          '<div class="message-time ' + timeAlignmentClass + '">' + formattedTime + '</div>' +
                          '</div>';
        }
                    output.append(messageHtml);
                });
            }
        },
        error: function() {
            alert('メッセージの読み込みに失敗しました。');
        }
    });
}


$(document).ready(function() {
    loadMessages(); // ページ読み込み時にメッセージを読み込む

    $('#send').click(function(e) {
        e.preventDefault();
        var content = $('input[name="content"]').val();
        if (!content) {
            alert('メッセージを入力してください。');
            return;
        }
        $.ajax({
            url: 'chat_rw.php',
            type: 'POST',
            data: {
                user_id: $('input[name="user_id"]').val(),
                user_name: $('input[name="user_name"]').val(),
                message_type: 'text', // message_typeの修正
                content: content
            },
            success: function(response) {
                $('input[name="content"]').val(''); // 入力フィールドをクリア
                loadMessages(); // メッセージを再読込
                document.getElementById("mySound").play(); // ここで音声を再生
            },
            error: function(xhr, status, error) {
                alert('メッセージの送信に失敗しました。エラー: ' + xhr.responseText);
            }
        });
    });

    // ここでは静的なファイル名の配列から取得
    const stamps = [
        'denwa.jpg', 'genki.jpg', 'gomen.jpg',
        'samisi.jpg', 'tabeteru.jpg', 'tanosii.jpg', 'uresii.jpg', 'ok.jpg'
    ];

    const gallery = document.getElementById('stamp-gallery');

    stamps.forEach(stamp => {
        const img = document.createElement('img');
        img.src = 'stdata/' + stamp;
        img.alt = 'スタンプ';
        img.onclick = function() {
            const isConfirmed = confirm('このスタンプを送信しますか？');
            if (isConfirmed) {
                $.ajax({
                    url: 'chat_rw.php',
                    type: 'POST',
                    data: {
                        user_id: $('input[name="user_id"]').val(),
                        user_name: $('input[name="user_name"]').val(),
                        message_type: 'stamp',
                        content: 'stdata/' + stamp
                    },
                    success: function(response) {
                        loadMessages(); // メッセージを再読込
                        document.getElementById("mySound").play(); // ここで音声を再生
                    },
                    error: function(xhr, status, error) {
                        console.error('エラーが発生しました: ' + error);
                    }
                });
            } else {
                console.log('送信がキャンセルされました。');
            }
        };
        gallery.appendChild(img);
    });
});


</script>
