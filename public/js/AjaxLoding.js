function AjaxLoding(containerDivName)
{
	this.root=document.getElementById(containerDivName);
	this.reading=false;
}
AjaxLoding.prototype.ajax=function(method, url, opt){
	/* opt = {
		'type':'arraybuffer'/'blob'/'document'/'json'/'text'
		'params':{
			'key':value
		},
		'success':function(data),
		'error':function(xhr)
		
	}
	*/
	//限制一次只有一個 ajax
	if(this.reading){
		return;
	}
	this.reading=true;
	method=method.toUpperCase();
	var that=this;
	var xhr=new XMLHttpRequest();
	xhr.open(method,url);
	xhr.responseType=(opt.type?opt.type:'');
	if(method==='POST'){
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	}
	xhr.onreadystatechange=function (){
		if(xhr.readyState==4){
			that.reading=false;
			that.root.style.display='none';
			if(this.status==200){
				if(opt.success){
					opt.success(this.response);
				}
			} else {
				if(opt.error){
					opt.error(this);
				}
			}
		}
	}
	this.root.style.display='';
	let paramArr=[];
	if(opt.params){
		for(let k in opt.params){
			paramArr.push(k+'='+encodeURIComponent(opt.params[k]));
		}
	}
	xhr.send(paramArr.join('&'));
}