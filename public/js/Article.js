function Article(baseUrl,coder,ajaxLoder,typeId,titleId,contentId,previewTitleId,previewId,onIdChange)
{
	//唯讀屬性
	let datas={
		'baseUrl':baseUrl,
		'coder':coder,
		'ajaxLoder':ajaxLoder,
		'eleType':document.getElementById(typeId),
		'eleTitle':document.getElementById(titleId),
		'eleContent':document.getElementById(contentId),
		'elePreviewTitle':document.getElementById(previewTitleId),
		'elePreview':document.getElementById(previewId),
		'onIdChange':onIdChange
	}
	//讀寫屬性
	let datas2={
		'_docId':false,
		'origTitle':'',
		'origContent':'',
		'origType':0,
		'format':'text'
	}
	
	for(let k in datas){
		Object.defineProperty(this,k,{
			value:datas[k]
		});
	}
	for(let k in datas2){
		Object.defineProperty(this,k,{
			value:datas2[k],
			writable:true
		});
	}
	//
	//this.markdownContainer=this.elePreview.attachShadow({mode: 'closed'});
	this.randerContent=this.createDelayFunc(function(){
		if(this.docType===1){
			this.elePreview.className='github-markdown';
			this.elePreview.innerHTML=marked(this.eleContent.value,{gfm:true});
		} else {
			this.elePreview.className='plain-text';
			this.elePreview.textContent=this.eleContent.value;
		}
	},300);
	
	//title 綁定
	Object.defineProperty(this,'title',{
		get:function(){
			return this.eleTitle.value;
		},
		set:function(val){
			this.eleTitle.value=val;
			this.elePreviewTitle.textContent=val;
		}
	});
	
	//content 綁定
	Object.defineProperty(this,'content',{
		get:function(){
			return this.eleContent.value;
		},
		set:function(val){
			this.eleContent.value=val;
			this.randerContent();
			//this.elePreview.textContent=val;
		}
	});
	
	//docType 綁定
	Object.defineProperty(this,'docType',{
		get:function(){
			return parseInt(this.eleType.value,10);
		},
		set:function(val){
			var n;
			for(n=this.eleType.options.length-1;n>=0;--n){
				if(this.eleType.options[n].value==val){
					this.eleType.selectedIndex=n;
					break;
				}
			}
		}
	});
	
	//docId 綁定
	Object.defineProperty(this,'docId',{
		get:function(){
			return this._docId;
		},
		set:function(val){
			this._docId=val===false?false:parseInt(val,10);
			if(this.onIdChange){
				this.onIdChange(this._docId);
			}
		}
	});
	
	
	
	this.eleContent.addEventListener('keyup',this.randerContent);
	this.eleType.addEventListener('change',this.randerContent);
	this.eleTitle.addEventListener('keyup',this.createDelayFunc(function(){
		this.elePreviewTitle.textContent=this.eleTitle.value;
	},300));
}

//確認文章變更提示
Article.prototype.changeCheck=function()
{
	if((this.origTitle !== this.title || this.origContent !== this.content || this.origType !== this.docType)){
		let cfm=confirm('放棄已經修改的內容？');
		return cfm;
	}
	return true;
}

//開新文件
Article.prototype.newDoc=function()
{
	if(!this.changeCheck()){
		return false;
	}
	this.docId=false;
	this.title='新文章';
	this.content='';
	this.docType=0;
	this.origTitle=this.title;
	this.origContent='';
	this.origType=0;
	return true;
}

//取消變更
Article.prototype.undo=function()
{
	if(!this.changeCheck()){
		return false;
	}
	this.title=this.origTitle;
	this.content=this.origContent;
	this.docType=this.origType;
	return true;
}

//從伺服器讀取文章內容
Article.prototype.load=function(id, success, error)
{
	if(!this.changeCheck()){
		return;
	}
	var that = this;
	this.ajaxLoder.ajax('get',this.baseUrl+'getDoc.php?id='+id,{
		'type':'json',
		'success':function(data){
			that.docId=id;
			that.title=that.coder.dec(data['title']).str('utf8');
			var ut8rr=that.coder.dec(data['content']);
			that.docType=ut8rr[0];
			that.content=ut8rr.slice(1).str('utf8');
			that.origTitle=that.title;
			that.origContent=that.content;
			that.origType=that.docType;
			if(success){
				success(data);
			}
		},
		'error':function(xhr){
			if(error){
				error(xhr);
			}
		}
	});
}

//儲存文章到伺服器
Article.prototype.save=function(success, error)
{
	if(this.origTitle === this.title && this.origContent === this.content && this.origType === this.docType){
		return;
	}
	var that = this;
	var u8arr=this.content.bin('utf8');
	var contentU8Arr=new Uint8Array(u8arr.length+1);
	contentU8Arr.set(u8arr,1);
	contentU8Arr[0]=parseInt(this.eleType.value,10);
	var params={
		'title':this.coder.enc(this.title.bin('utf8')),
		'content':this.coder.enc(contentU8Arr)
	};
	if(this.docId){
		params['id']=this.docId;
	}
	this.ajaxLoder.ajax('post',this.baseUrl+'saveDoc.php',{
		'type':'text',
		'params':params,
		'success':function(data){
			that.origTitle=that.title;
			that.origContent=that.content;
			that.origType=that.docType;
			if(that.docId===false){
				that.docId=parseInt(JSON.parse(data)['docId'],10);
			}
			that.randerContent();
			if(success){
				success(that.docId);
			}
		},
		'error':function(xhr){
			if(error){
				error(xhr);
			}
		}
	});
}

//刪除文章
Article.prototype.del=function(success, error)
{
	if(!this.docId){
		return;
	}
	if(!confirm('確定刪除文章 '+this.title+'？')){
		return;
	}
	var that=this;
	
	this.ajaxLoder.ajax('post',this.baseUrl+'delDoc.php',{
		'type':'text',
		'params':{'id':this.docId},
		'success':function(data){
			that.docId=false;
			that.title='';
			that.content='';
			that.origTitle='';
			that.origContent='';
			that.docType=0;
			that.origType=0;
			if(success){
				success(that.docId);
			}
		},
		'error':function(xhr){
			if(error){
				error(xhr);
			}
		}
	});
}

Article.prototype.createDelayFunc=function(cbk,itv)
{
	let timer=false;
	let last_t=0;
	let param;
	let that=this;
	function proc()
	{
		let t=Date.now();
		if(t-last_t>=itv){
			timer=false;
			cbk.apply(that,Array.prototype.slice.call(param));
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