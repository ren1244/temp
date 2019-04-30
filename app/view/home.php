<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>個人文章</title>
    <script src="<?php echo env::baseUrl;?>public/js/binJS.min.js" type="text/javascript"></script>
    <script src="<?php echo env::baseUrl;?>public/js/dataloder.js" type="text/javascript"></script>
    <script src="<?php echo env::baseUrl;?>public/js/AjaxLoding.js" type="text/javascript"></script>
    <script src="<?php echo env::baseUrl;?>public/js/Coder.js" type="text/javascript"></script>
    <script src="<?php echo env::baseUrl;?>public/js/marked.min.js" type="text/javascript"></script>
    <script src="<?php echo env::baseUrl;?>public/js/Article.js" type="text/javascript"></script>
    <script src="<?php echo env::baseUrl;?>public/js/ArticleIndex.js" type="text/javascript"></script>    
    <script src="<?php echo env::baseUrl;?>public/js/home.js" type="text/javascript"></script>
    <link href="<?php echo env::baseUrl;?>public/css/home.css" rel="stylesheet" type="text/css">
</head>
<body>
    <script>
        var baseUrl='<?php echo env::baseUrl;?>';
        var salt='<?php echo $salt;?>';
        var encSalt='<?php echo $encSalt;?>';
    </script>
    
    <!-- 選單 -->
    <div id='menu'>
        <ul class='row'>
            <li id='newArticle' class='col'>新文章</li>
            <?php if($admin):?><li id='admin' class='col'>管理</li><?php endif;?>
            <li id='setting' class='col'>設定</li>
            <li id='logout' class='col'>登出</li>
        </ul>
        <ul id='directory'>
        </ul>
    </div>
    
    <!-- 內容 -->
    <div id='content'>
        <div id='header'>
            <span id='viewTitle'>新文章</span>            
            <div>
                <img id='img-more' src='<?php echo env::baseUrl;?>public/img/ic_more_vert_18px.svg'>
                <img id='img-close' style='display:none' src='<?php echo env::baseUrl;?>public/img/ic_close_18px.svg'>
                <ul id='mode2' style='display:none'>
                    <li id='evtEdit'>編輯</li>
                    <li id='evtDel'>刪除</li>
                </ul>
            </div>
        </div>  
        <div id='mode'>
            <ul>
                <li id='back2menu' style='display:none'>目錄</li>
                <li id='back2article' style='display:none'>返回</li>
                <li id='readMode'>閱讀</li>
                <li id='readMode2'>夜間</li>
                <li id='convtw'>繁體</li>
            </ul>
        </div>
        <div id='article'></div>
    </div>
    
    <!-- 編輯器 -->
    <div id='editor' style='display:none'>
        <div>
            <img id='img-back' width='36px' src='<?php echo env::baseUrl;?>public/img/ic_close_18px.svg'>
            <img id='img-save' width='36px' src='<?php echo env::baseUrl;?>public/img/ic_save_48px.svg'>
        </div>
        <div class='row'>
            標題
            <input id='title' type='text' class='col'>
            <select id='docType'>
                <option value='0'>純文字</option>
                <option value='1'>markdown</option>
            </select>
        </div>
        <textarea id='articleContent'></textarea>
    </div>
    
    <!--讀取中的動畫-->
    <div id='loding' style='display:none'>
        <div class='lds-spinner-container'>
            <div class="lds-spinner">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class='text'><div><span>載入中</span></div></div>
        </div>
    </div>
</body>
</html>
