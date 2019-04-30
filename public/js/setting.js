let ele={};
let entities={};
function ajaxSender(cfg)
{
	let preset={
		url:'#',
		method:'GET',
		responseType:'text',
		param:{}
	}
	for(let key in preset){
		if(cfg[key]===undefined){
			cfg[key]=preset[key];
		}
	}
	
	let paramList=[];
	for(let key in cfg.param){
		paramList.push(key+'='+encodeURIComponent(cfg.param[key]));
	}
	
	if(cfg.method.toUpperCase()==='GET'){
		cfg.url+='?'+paramList.join('&');
		paramList=[];
	}
	
	var xhr=new XMLHttpRequest();
	xhr.open(cfg.method,cfg.url);
	xhr.responseType=cfg.responseType;
	if(cfg.method.toUpperCase()==='POST'){
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	}
	xhr.onreadystatechange=function (){
		if(this.readyState==4){
			if(this.status==200){
				if(cfg.success){
					cfg.success(this.response);
				}
			} else {
				if(cfg.error){
					cfg.error(this);
				}
			}
		}
	}
	xhr.send(paramList.join('&'));
}


let chpwd=(function(){
	let status=0;
	let newHash2,newCoder,newSalt,newPwd;
	return function (evt){
		evt.preventDefault();
		
		let pwd1=document.getElementById('new-pwd-1').value;
		let pwd2=document.getElementById('new-pwd-2').value;
		if(pwd1!==pwd2 || pwd1.length<4){
			alert('需要大於4個字元');
			return;
		}
		newPwd=pwd1;
		//let pSalt=JSON.parse(sessionStorage.getItem('pSalt'));
		//newHash2=pwd1.bin('utf8').hmac_sha2('256',pSalt);
		//newCoder=new Coder(newHash2.hmac_sha2('256',encSalt));
		//newHash2=newHash2.str('hex');
		start();
	}
	
	function start(){
		ajaxSender({
			url:baseUrl+'app/api/chpwd/start.php',
			method:'POST',
			responseType:'json',
			success:createUpdateFunc(),
			error:function(xhr){
				console.log(xhr.status);
			}
		});
	}
	
	function createUpdateFunc(){
		//單線更新
		let dataList;
		let idx=0;
		//title, content, chpwd=1
		return function(data){
			if(!confirm('變更密碼必須對檔案重新加密，根據資料多寡可能需要一點時間，是否繼續？')) {
				return;
			}
			ele['log'].removeAttribute('style');
			ele['log'].textContent='讀取文章列表準備重新加密...';
			newSalt=data['newSalt'];
			dataList=data['data'];
			newHash2=newPwd.bin('utf8').hmac_sha2('256',newSalt);
			newCoder=new Coder(newHash2.hmac_sha2('256',encSalt));
			newHash2=newHash2.str('hex');
			readForUpdate();
		}
		
		function readForUpdate(){
			if(idx>=dataList.length){
				end();
				return;
			}
			let title=entities['coder'].dec(dataList[idx].title);
			ele['log'].textContent='讀取 '+title;
			ajaxSender({
				url:baseUrl+'app/api/doc/getDoc.php',
				method:'GET',
				responseType:'json',
				param:{
					id:dataList[idx].id
				},
				success:function(d){
					let title=entities['coder'].dec(d.title);
					let content=entities['coder'].dec(d.content);
					ele['log'].textContent='更新 '+title;
					update(title, content);
				},
				error:onChpwdError
			});
		}
		
		function update(title, content){
			ajaxSender({
				url:baseUrl+'app/api/doc/saveDoc.php',
				method:'POST',
				param:{
					title:newCoder.enc(title),
					content:newCoder.enc(content),
					chpwd:1
				},
				success:function(){
					++idx;
					readForUpdate();
				},
				error:onChpwdError
			});
		}
	}
	
	function end(){
		let hash=document.getElementById('new-pwd-1').value
		        .bin('utf8').hmac_sha2('256',salt).str('hex');
		ajaxSender({
			url:baseUrl+'app/api/chpwd/finish.php',
			method:'POST',
			param:{
				hash:hash
			},
			success:function(){
				sessionStorage.setItem('pwd',newHash2);
				entities['coder']=newCoder;
				ele['log'].textContent='密碼變更完成，即將登出';
				setTimeout(function(){
					logout();
				},2000);
			},
			error:onChpwdError
		});
	}
	
})();

function onChpwdError()
{
	ele['log'].textContent='密碼變更失敗';
	setTimeout(function(){
		ele['log'].style.display='none';
	},2000);
}

document.addEventListener("DOMContentLoaded",function(evt){
	ele['form']=document.getElementById('chpwd-form');
	ele['form'].addEventListener('submit',chpwd,false);
	ele['log']=document.getElementById('log');
	//讀取舊的密碼
	let pwd=sessionStorage.getItem('pwd');
	let pSalt=sessionStorage.getItem('pSalt');
	if(!pwd || !pSalt){
		logout();
		return;
	}
	entities['coder']=new Coder(pwd.bin('hex').hmac_sha2('256',encSalt));
	document.getElementById('back').addEventListener('click',function(){
		location.replace(baseUrl);
	});
});

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

