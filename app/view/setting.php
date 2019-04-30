<!DOCTYPE html>
<html lang="Zh-Hant">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>設定</title>
    <link href="<?php echo $resourceDir;?>/css/setting.css" rel="stylesheet" type="text/css">
    <script src="<?php echo $resourceDir;?>/js/binJS.min.js" type="text/javascript"></script>
    <script src="<?php echo $resourceDir;?>/js/Coder.js" type="text/javascript"></script>
    <script src="<?php echo $resourceDir;?>/js/setting.js" type="text/javascript"></script>
</head>
<body>
    <script>
        var baseUrl='<?php echo env::baseUrl;?>';
        var salt='<?php echo $salt;?>';
        var encSalt='<?php echo $encSalt;?>';
    </script>
    <span id='back'><img class='backimg' src='<?php echo env::baseUrl;?>public/img/ic_reply_24px.svg'>返回</span>
    <ul id='nav'>
        <li data-div-id='chpwd-tab' class='nav-select'>變更密碼</li>
    </ul>
    <hr>
    <form id='chpwd-form'>        
        <div>
            <input id='new-pwd-1' type='password' placeholder="新密碼">
        </div>
        <div>
            <input id='new-pwd-2' type='password' placeholder="新密碼再確認">
        </div>
        <div>
            <input type='submit' value='傳送'>
        </div>
    </form>
    <div id='log' style='display:none'></div>
</body>
</html>
