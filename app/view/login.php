<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>登入</title>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo env::googleRecaptcha['public'];?>"></script>
    <script src="<?php echo env::baseUrl;?>public/js/jquery-3.3.1.min.js" type="text/javascript"></script>
    <script src="<?php echo env::baseUrl;?>public/js/binJS.min.js" type="text/javascript"></script>
    <script src="<?php echo env::baseUrl;?>public/js/login.js" type="text/javascript"></script>
    <style>
        html,body{
            height:100%;
            margin:0px;
            padding:0px;
        }
        body{
            display:flex;
            justify-content:center;
            align-items:center;
        }
        input[type='text'],input[type='password'],input[type='email'],input[type='submit']{
            box-sizing:border-box;
            width:200px;
        }
        #box{
            display:inline-block;
            width:320px;
            height:240px;
            padding:10px;
        }
        #nav{
            display:flex;
        }
        .nav-bt{
            flex-grow:1;
            text-align:center;
            border:1px solid lightgray;
            cursor:default;
        }
        .nav-bt-select{
            border:1px solid gray;
        }
        #content{
            border:1px solid gray;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100%;
        }
        #reg-run,#login{
            background-color:#CCC;
        }
    </style>
</head>
<body>
    <script>
        var baseUrl='<?php echo env::baseUrl;?>';
        var salt='<?php echo $salt;?>';
        var gToken=false;
        grecaptcha.ready(function() {
            grecaptcha.execute(
                '<?php echo env::googleRecaptcha['public'];?>',
                {action: 'login'}
            ).then(function(token){
                gToken=token;
            });
        });
    </script>
    <div id='box'>
        <div id='nav'>
            <div id='nav-login' class='nav-bt nav-bt-select' data-id='loginDiv'>登入</div>
            <div id='nav-reg' class='nav-bt' data-id='regDiv'>註冊</div>
        </div>
        <div id='content'>
            <div id='loginDiv'>
                <table>
                    <tr>
                        <td><input id='user' type='text' placeholder='使用者帳號' autofocus></td>
                    </tr>
                    <tr>
                        <td><input id='pwd' type='password' placeholder='密碼'></td>
                    </tr>
                    <tr>
                        <td><input id='remember' type='checkbox'><label for="remember">記住我</label></td>
                    </tr>
                    <tr>
                        <td><input id='login' type='submit' value='登入'></td>
                    </tr>
                </table>
                
            </div>
            <div id='regDiv' style='display:none'>
                <table>
                    <tr>
                        <td>*</td>
                        <td><input id='reg-user' type='text' placeholder='帳號'></td>
                    </tr>
                    <tr>
                        <td>*</td>
                        <td><input id='reg-pwd' type='password' placeholder='密碼'></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>*</td>
                        <td><input id='reg-pwd2' type='password' placeholder='確認密碼'></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input id='reg-email' type='email' placeholder='電子信箱'></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>*</td>
                        <td><input id='reg-token' type='text' placeholder='驗證碼'></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style='text-align:right'><input id='reg-run' type='submit' value='註冊'></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
        <div id='log'></div>
    </div>
</body>
</html>
