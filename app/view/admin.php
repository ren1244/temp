<!DOCTYPE html>
<html lang="Zh-Hant">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>管理</title>
    <script src="<?php echo env::baseUrl;?>public/js/admin.js" type="text/javascript"></script>
    <link href="<?php echo env::baseUrl;?>public/css/admin.css" rel="stylesheet" type="text/css">
</head>
<body>
    <script>
        var baseUrl="<?php echo env::baseUrl;?>";
    </script>
    <span id='back'><img class='backimg' src='<?php echo env::baseUrl;?>public/img/ic_reply_24px.svg'>返回</span>
    <ul id='nav'>
        <li data-div-id='authCode'>認證碼</li>
        <li data-div-id='users'>使用者</li>
    </ul>
    <hr>
    <div id='tab-list'>
        <div id='authCode'>
            <table>
                <thead>
                    <tr>
                        <th>認證碼</th>
                        <th><span class='add' id='add'>新增</span></th>
                    </tr>
                </thead>
                <tbody id='tb-content'></tbody>
            </table>
        </div>
        <div id='users'>
            <table>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>名稱</th>
                        <th>權限</th>
                        <th>e-mail</th>
                        <th>建立時間</th>
                        <th>變更時間</th>
                        <th><!--操作--></th>
                    </tr>
                </thead>
                <tbody id='user-list'></tbody>
            </table>
        </div>
    </div>
</body>
</html>
