$(function(){
	$('#reg-run').click(regFunc);
});

function regFunc()
{
	let user=$('#reg-user').val(),
	    pwd=$('#reg-pwd').val(),
	    pwd2=$('#reg-pwd2').val(),
	    email=$('#reg-email').val(),
	    token=$('#reg-token').val(),
		log=[];
	if(user.length<4){
		log.push('帳號至少要4個字元');
	}
	if(pwd!=pwd2){
		log.push('兩次密碼輸入不相等');
	}
	if(token.length===0){
		log.push('尚未輸入驗證碼');
	}
	if(log.length>0){
		$('#reg-log').html('<p>'+log.join('</p><p>')+'</p>');
	}
}