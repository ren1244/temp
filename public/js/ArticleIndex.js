function ArticleIndex(baseUrl,coder,ajaxLoder,containerId,trigger)
{
	let datas={
		'baseUrl':baseUrl,
		'coder':coder,
		'ajaxLoder':ajaxLoder,
		'eleContainer':document.getElementById(containerId),
		'trigger':trigger
	}
	
	for(let k in datas){
		Object.defineProperty(this,k,{
			value:datas[k]
		});
	}
	
	Object.defineProperty(this,'listArray',{
		value:[],
		writable:true
	});
	
	Object.defineProperty(this,'list',{
		get:function(){
			return this.listArray;
		},
		set:function(arr){
			let liArray=arr.map(function(x){
				return '<li data-id="'+x.id+'">'+(x.title?x.title:'(無標題)')+'</li>'
			});
			this.eleContainer.innerHTML=liArray.join('');
			for(let n=this.eleContainer.children.length-1;n>=0;--n){
				let x=this.eleContainer.children[n];
				x.addEventListener('click',this.trigger);
			}
			
			
			this.listArray=arr;
		}
	});
}

//取得目錄
ArticleIndex.prototype.load=function(success, error)
{
	var that=this;
	
	this.ajaxLoder.ajax('get',this.baseUrl+'getDir.php',{
		'type':'json',
		'success':function(data){
			let arr=data;
			for(let k in arr){
				arr[k].title=that.coder.dec(arr[k].title).str('utf8');
			}
			that.list=arr;
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