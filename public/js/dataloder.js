//ar gArr;

function DataLoader()
{
	
}

DataLoader.prototype.read=function(urlList,callback) //讀取
{
	this.parts=[];
	this.urlList=urlList;
	this.callback=callback;
	this.loadedCount=0;
	for(let i=0;i<urlList.length;++i){
		this.getResource(i,urlList[i]);
	}
}

DataLoader.prototype.getResource=function(idx,url)
{
	var that=this;
	var img=new Image();
	img.onload=function(evt){
		var w=img.width;
		var h=img.height
		var cvs=document.createElement("canvas");
		cvs.width=w;
		cvs.height=h;
		var ctx=cvs.getContext('2d');
		ctx.drawImage(img,0,0);
		var imgData=ctx.getImageData(0,0,w,h);
		var data=imgData.data;
		var arr=new Uint8Array(w*h*3);
		var i,n=data.length,k;
		for(i=0;i<n;i+=4)
		{
			k=i/4*3;
			arr[k]=data[i];
			arr[k+1]=data[i+1];
			arr[k+2]=data[i+2];
		}
		that.parts[idx]=arr;
		if(++that.loadedCount==that.urlList.length)
			that.mergeAndExec();
	}
	img.src=url;
}

DataLoader.prototype.mergeAndExec=function(){
	var buf=this.parts;
	var sz=(buf[0][0]|buf[0][1]<<8|buf[0][2]<<16|buf[0][3]<<24);
	var i,offset=0,len,j;
	var arr=new Uint8Array(sz);
	//buf[0]=buf[0].slice(4);
	var tmp=new Uint8Array(buf[0].length-4);
	for(i=4;i<buf[0].length;++i)
		tmp[i-4]=buf[0][i];
	buf[0]=tmp;
	for(i=0;i<buf.length;++i)
	{
		if(offset+buf[i].length>sz){
			//buf[i]=buf[i].slice(0,sz-offset);
			tmp=new Uint8Array(sz-offset);
			for(j=0;j<sz-offset;++j)
				tmp[j]=buf[i][j];
			buf[i]=tmp;
		}
		arr.set(buf[i],offset);
		offset+=buf[i].length;
	}
	this.callback(arr);
}
