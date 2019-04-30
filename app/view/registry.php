<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>註冊</title>
    <script src="<?php echo env::baseUrl;?>public/js/jquery-3.3.1.min.js" type="text/javascript"></script>
    <script src="<?php echo env::baseUrl;?>public/js/binJS.min.js" type="text/javascript"></script>
    <script src="<?php echo env::baseUrl;?>public/js/registry.js" type="text/javascript"></script>
</head>
<body>
    <script>
        var baseUrl='<?php echo env::baseUrl;?>';
        var salt='<?php echo $salt;?>';
    </script>
    <h3>註冊</h3>
    <div id='registry'>
        帳號 <input id='reg-user' type='text'><br>
        密碼 <input id='reg-pwd' type='password'><br>
        密碼 <input id='reg-pwd2' type='password'>(確認)<br>
        信箱 <input id='reg-email' type='email'>(選填，密碼重置用)<br>
        驗證 <input id='reg-token' type='text'><br>
        <input id='reg-run' type='submit' value='註冊'>
        <div id='reg-log'></div>
    </div>
</body>
</html>
