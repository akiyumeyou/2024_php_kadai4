
        $(document).ready(function() {
            // メッセージを非同期で読み込む関数
            function loadMessages() {
                $.ajax({
                    url: 'chat_rw.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#output').empty();
                        if (data.length > 0) {
    $.each(data, function(i, message) {
        // 名前とメッセージ、時間を表示
        var messageHtml = '<div class="message-box">' +
                          '<div class="user-name">投稿者: ' + $('<div/>').text(message.user_name).html() + '</div>' +
                          '<div class="message-content">' + $('<div/>').text(message.content).html() +
                          '<span class="message-time">' + message.timestamp + '</span>' + '</div>' +
                          '</div>';
        $('#output').append(messageHtml);
    });
} else {
    $('#output').append('<p>メッセージはありません。</p>');
}
                    }}}}}
