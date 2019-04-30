function Coder(sKey)
{
	Object.defineProperty(this,'sKey',{
		value:sKey
	});
}

//加密
Coder.prototype.enc=function(u8arr)
{
	let iv=new Uint8Array(16);
	for(let i=0;i<16;++i)
		iv[i]=(Math.random()*256&0xFF);
	let c=u8arr.cipher('aes_cbc_pkcs7',this.sKey,iv);
	let arr=new Uint8Array(16+c.length);
	arr.set(iv);
	arr.set(c,16);
	return arr.str('base64').replace(/[\+\/\=]/g,function(m){
		return {'+':'-','/':'_','=':''}[m];
	});
}

//解密
Coder.prototype.dec=function(str)
{
	let b64=str.replace(/[\-_]/g,function(m){
		return {'-':'+','_':'/'}[m];
	})+('===='.substr(0,(4-str.length%4)%4));
	let data=b64.bin('base64');
	let iv=data.slice(0,16);
	let plain=data.slice(16).decipher('aes_cbc_pkcs7',this.sKey,iv);
	return plain;
}