document.addEventListener("DOMContentLoaded",function(evt){
	//
	getTokens(tbRefresh);
	document.getElementById('add').addEventListener('click',function(){
		addToken(tbAdd);
	});
	
	document.getElementById('back').addEventListener('click',function(){
		location.replace(baseUrl);
	});
	
	function tbAdd(data) {
		tbUpdate(data,false);
	}
	
	function tbRefresh(data) {
		tbUpdate(data,true);
	}
	
	navInit();
	refershUserList();
});

function getTokens(callback)
{
	var xhr=new XMLHttpRequest();
	xhr.responseType='json';
	xhr.open('GET',baseUrl+'app/api/user/getToken.php');
	xhr.onreadystatechange=function (){
		if(this.readyState==4){
			if(this.status==200){
				if(callback) {
					callback(this.response);
				}
			}
		}
	}
	xhr.send();
}

function addToken(callback)
{
	var xhr=new XMLHttpRequest();
	xhr.responseType='json';
	xhr.open('POST',baseUrl+'app/api/user/addToken.php');
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	xhr.onreadystatechange=function (){
		if(this.readyState==4){
			if(this.status==200){
				if(callback) {
					callback(this.response);
				}
			}
		}
	}
	xhr.send();
}

function delToken(token, callback)
{
	var xhr=new XMLHttpRequest();
	xhr.responseType='json';
	xhr.open('POST',baseUrl+'app/api/user/deleteToken.php');
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	xhr.onreadystatechange=function (){
		if(this.readyState==4){
			if(this.status==200){
				if(callback) {
					callback(this.response);
				}
			}
		}
	}
	xhr.send('token='+encodeURIComponent(token));
}

function tbUpdate(tokenList,removeOld)
{
	let frg=document.createDocumentFragment();
	let tbody=document.getElementById('tb-content');
	for(let k in tokenList) {
		let v=tokenList[k];
		let tr=document.createElement('tr');
		let td=document.createElement('td');
		frg.appendChild(tr);
		tr.appendChild(td);
		tr.setAttribute('id','token-'+v['token']);
		td.textContent=v['token'];
		td=document.createElement('td');
		let span=document.createElement('span');
		td.appendChild(span);
		span.textContent='刪除';
		span.dataset.token=v['token'];
		span.addEventListener('click',tbDeleteRow);
		tr.appendChild(td);
	}
	if(removeOld) {
		tbody.innerHTML='';
	}
	tbody.appendChild(frg);
}

function tbDeleteRow(evt)
{
	let token=this.dataset.token;
	let tr=document.getElementById('token-'+token);
	delToken(token, function(data){
		if(data['rowCount']>0){
			tr.parentElement.removeChild(tr);
		}
	});
}



function navInit()
{
	let nav=document.getElementById('nav');
	let current;
	for(let i=nav.children.length-1; i>=0;--i) {
		let li=nav.children[i];
		let div=document.getElementById(li.dataset.divId);
		if(i!==0) {
			div.style.display='none';
			li.classList.add('nav-unselect');
		} else {
			li.classList.add('nav-select');
			current=li;
		}
		li.addEventListener('click',tabChange);
	}
	
	function tabChange(evt) {
		let li=evt.target;
		if(li===current) {
			return;
		}
		let div1=document.getElementById(current.dataset.divId);
		current.classList.remove('nav-select');
		current.classList.add('nav-unselect');
		let div2=document.getElementById(li.dataset.divId);
		li.classList.remove('nav-unselect');
		li.classList.add('nav-select');
		div1.style.display='none';
		div2.removeAttribute('style');
		current=li;
	}
}

function getUserList(success, error)
{
	var xhr=new XMLHttpRequest();
	xhr.open('GET',baseUrl+'app/api/user/getUserList.php');
	xhr.responseType='json';
	xhr.onreadystatechange=function (){
		if(this.readyState==4){
			if(this.status==200){
				if(success) {
					success(this.response);
				}
			} else {
				if(error) {
					error(this);
				}
			}
		}
	}
	xhr.send();
}

function deleteUser(id, success, error)
{
	var xhr=new XMLHttpRequest();
	xhr.open('POST',baseUrl+'app/api/user/deleteUser.php');
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	xhr.onreadystatechange=function (){
		if(this.readyState==4){
			if(this.status==200){
				if(success) {
					success(this.response);
				}
			} else {
				if(error) {
					error(this);
				}
			}
		}
	}
	xhr.send('uid='+id);
}

function setAdmin(id, admin, success, error)
{
	var xhr=new XMLHttpRequest();
	xhr.open('POST',baseUrl+'app/api/user/setAdmin.php');
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	xhr.onreadystatechange=function (){
		if(this.readyState==4){
			if(this.status==200){
				if(success) {
					success(this.response);
				}
			} else {
				if(error) {
					error(this);
				}
			}
		}
	}
	xhr.send('uid='+id+"&val="+(admin?1:0));
}

function refershUserList()
{
	getUserList(function(data){
		let list=document.getElementById('user-list');
		let html=[];
		data.forEach(function(row,i){
			html.push('<tr>');
			html.push('<td>'+row['id']+'</td>');
			html.push('<td>'+row['name']+'</td>');
			html.push('<td><input id="user-ck-'+row['id']+'" data-uid="'+row['id']+'" type="checkbox"></td>');
			html.push('<td>'+row['email']+'</td>');
			html.push('<td>'+row['create_time']+'</td>');
			html.push('<td>'+row['update_time']+'</td>');
			html.push('<td><button id="user-bt-'+row['id']
			+'" data-uid="'+row['id']
			+'" data-uname="'+row['name']
			+'">刪除</button></td>');
			html.push('</tr>');
		});
		list.innerHTML=html.join('\n');
		data.forEach(function(row){
			let id=row['id'];
			let bt=document.getElementById('user-bt-'+id);
			let ck=document.getElementById('user-ck-'+id);
			if(parseInt(row['admin'],10)===1) {
				ck.checked=true;
			}
			bt.addEventListener('click',function(evt) {
				let id=evt.target.dataset.uid;
				let that=evt.target;
				if(!confirm('確定刪除"'+evt.target.dataset.uname+'"?')){
					return;
				}
				evt.target.disabled=true;
				deleteUser(id,function(data){
					let p=that;
					while(p.tagName && p.tagName!=='TR') {
						p=p.parentElement;
					}
					if(!p) {
						return;
					}
					p.parentElement.removeChild(p);
				},function(xhr){
					evt.target.disabled=false;
				});
			});
			ck.addEventListener('change',function(evt){
				let id=evt.target.dataset.uid;
				let admin=evt.target.checked;
				evt.target.disabled=true;
				setAdmin(id, admin, function(){
					evt.target.disabled=false;
				},function(){
					evt.target.checked=!evt.target.checked;
					evt.target.disabled=false;
				});
			});
		});
	},function(xhr){
		console.log(xhr.status+': '+xhr.responseText);
	});
}

