function getById(id)
{
	return document.getElementById(id);
}

function bindClick(id,cbk)
{
	document.getElementById(id).addEventListener('click',cbk);
}

function elementShow(id, st)
{
	var ele=document.getElementById(id);
	if(st){
		ele.removeAttribute('style');
	} else {
		ele.setAttribute('style','display:none');
	}
	return ele;
}
function delayExecute(cbk,itv){	
	let timer=false;
	let last_t=0;
	let param;
	function proc()
	{
		let t=Date.now();
		if(t-last_t>=itv){
			timer=false;
			cbk.apply(null,Array.prototype.slice.call(param));
		} else {
			timer=setTimeout(proc,itv-(t-last_t)+1);
		}
	}
	return function(){
		param=arguments;
		last_t=Date.now();
		if(timer===false){
			timer=setTimeout(proc,itv);
		}
	};
}
var entities={};

var refreshView;
function initRefreshViewFunc()
{
	var lastShowStatus=0;
	let k;
	let arr={
		'menu':[7,1],
		'content':[31,26],
		'editor':[4,4],
		'img-more':[7,7],
		'img-close':[0,0],
		'mode2':[0,0],
		'back2article':[24,24],
		'back2menu':[0,2],
		'header':[7,7],
		'readMode':[23,23],
		'readMode2':[15,15]
	};
	for(k in arr){
		arr[k].push(getById(k));
	}
	return function(showStatus){
		if(typeof(showStatus)!=='number'){
			showStatus=lastShowStatus;
		} else {
			lastShowStatus=showStatus;
		}
		let mode=getComputedStyle(document.body).display==='flex'?0:1;
		let mask=1<<showStatus;
		
		for(k in arr){
			if(mask & arr[k][mode]){
				arr[k][2].removeAttribute('style');
			} else {
				arr[k][2].setAttribute('style','display:none');
			}
		}
		if(entities['article'].docType===1){
			entities['article'].elePreview.className=(showStatus===4?'github-markdown-dark':'github-markdown');
		}
	}
}

document.addEventListener("DOMContentLoaded",function(evt){
	let pwd=sessionStorage.getItem('pwd');
	if(!pwd){
		logout();
		return;
	}
	let coder=new Coder(pwd.bin('hex').hmac_sha2('256',encSalt));
	let ajax=new AjaxLoding('loding');
	entities['article']=new Article(
		baseUrl+'app/api/doc/',
		coder,
		ajax,
		'docType',
		'title',
		'articleContent',
		'viewTitle',
		'article',
		refreshDirectoryStyle
	);
	entities['directory']=new ArticleIndex(
		baseUrl+'app/api/doc/',
		coder,
		ajax,
		'directory',
		loadArticle
	);
	initMenu();
	//載入簡轉繁模組
	var loder = new DataLoader();
	loder.read([
		baseUrl+'public/ndopcc00.png'
	],function(u8arr){
		let scpt=document.createElement('script');
		scpt.innerHTML=u8arr.str('utf8');
		document.body.appendChild(scpt);
		initContent();
	});
	//載入 markdown css
	(function(){
		function cssLoader(cssFileName){
			var xhr=new XMLHttpRequest();
			xhr.open('GET',baseUrl+'public/css/'+cssFileName);
			xhr.onreadystatechange=function (){
				if(this.readyState==4){
					if(this.status==200){
						let sty=document.createElement('style');
						sty.innerHTML=this.responseText;
						document.body.appendChild(sty);
					}
				}
			}
			xhr.send();
		}
		cssLoader('markdown.css');
		cssLoader('markdown-dark.css');
	})();
	
	initEditor();
	refreshView=delayExecute(initRefreshViewFunc(),50);
	refreshView();
	window.addEventListener('resize',refreshView);
});

function initMenu()
{
	//點選新文章
	bindClick('newArticle',function(){
		entities.article.newDoc();
		refreshView(2);
	});
	//點選設定
	bindClick('setting',function(){
		location.replace(baseUrl+'setting/');
	});
	//點選登出
	bindClick('logout',logout);
	//點選管理
	let admin=document.getElementById('admin');
	if(admin){
		bindClick('admin',function(){
			location.replace(baseUrl+'admin');
		});
	}
	refreshDirectory();
}

function logout()
{
	var xhr=new XMLHttpRequest();
	xhr.open('POST',baseUrl+'app/api/user/logout.php');
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	xhr.onreadystatechange=function (){
		if(xhr.readyState==4){
			if(xhr.status==200){
				location.replace(location.href);
			}
		}
	}
	xhr.send();
}

function initContent()
{
	//閱讀模式(日間)
	bindClick('readMode',function(){
		refreshView(3);
		getById('content').classList.remove('dark');
	});
	//閱讀模式(夜間)
	bindClick('readMode2',function(){
		refreshView(4);
		getById('content').classList.add('dark');
	});
	//返回
	bindClick('back2article',function(){
		refreshView(1);
		getById('content').classList.remove('dark');
	});
	//回到目錄
	bindClick('back2menu',function(){
		refreshView(0);
		getById('content').classList.remove('dark');
	});
	//轉繁體
	bindClick('convtw',function(){
		let opencc=require('node-opencc');
		let txt=opencc.simplifiedToTaiwan(entities['article'].content);
		if(entities['article'].docType===0){
			console.log(txt);
			getById('article').textContent=txt;
		} else {
			getById('article').innerHTML=marked(txt,{gfm:true});
		}
	});
	initEditOrDel();
	bindClick('content',function(){
		getById('img-close').click();
	});
	function initEditOrDel()
	{
		//點選編輯
		bindClick('evtEdit',function(evt){
			evt.stopPropagation();
			refreshView(2);
		});
		//點選刪除
		bindClick('evtDel',function(evt){
			evt.stopPropagation();
			elementShow('editor',0);
			refreshView(0);
			entities['article'].del(function(id){
				refreshDirectory();
			});
		});
		//點選展開
		bindClick('img-more',function(evt){
			evt.stopPropagation();
			showPanel();
		});
		//點選關閉
		bindClick('img-close',function(evt){
			evt.stopPropagation();
			hidePanel();
		});
		function hidePanel()
		{
			elementShow('mode2',0);
			elementShow('img-close',0);
			elementShow('img-more',1);
		}
		function showPanel()
		{
			elementShow('mode2',1);
			elementShow('img-close',1);
			elementShow('img-more',0);
		}
	}
}

function initEditor()
{
	//點選儲存
	bindClick('img-save',function(){
		entities['article'].save(function(xx){
			refreshView(1);
			refreshDirectory();
		},function(xhr){
			popupLog('儲存失敗 '+xhr.status)
		});
	});
	//點選取消
	bindClick('img-back',function(){
		if(entities['article'].undo()){
			if(entities['article'].docId===false){
				refreshView(0);
			} else {
				refreshView(1);
			}
		}
	});
	//允許 tab 鍵
	getById('articleContent').addEventListener('keydown',function(evt){
		let x=evt.target;
		if(evt.keyCode==9){
			let pos=x.selectionStart;
			x.value=x.value.slice(0,pos)+'\t'+x.value.slice(x.selectionEnd);
			x.setSelectionRange(pos+1,pos+1);
			evt.preventDefault();
		}
	});
}

function loadArticle()
{
	entities['article'].load(
		this.dataset.id,
		function(){
			refreshView(1);
		}
	);
}

function popupLog(log)
{
	alert(log);
}

function refreshDirectory()
{
	let id=entities['article'].docId;
	if(id===false){
		entities['article'].newDoc();
	}
	entities['directory'].load(function(){
		refreshDirectoryStyle(id);
	});
}

function refreshDirectoryStyle(id)
{
	let arr=getById('directory').children;
	for(n=arr.length-1;n>=0;--n){
		let liId=parseInt(arr[n].dataset.id,10);
		if(id===liId){
			arr[n].classList.add('dir-selected');
		} else {
			arr[n].classList.remove('dir-selected');
		}
	}
}
