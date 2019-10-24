$(function(){
	$('#login').click(loginFunc);
	$('#reg-run').click(regFunc);
	$('#nav-reg').click(changeTab);
	$('#nav-login').click(changeTab);
	$('#user,#pwd').on('change',function(){
		$('#log').text('');
	});
	serialInput(['user','pwd'],loginFunc);
	serialInput(['reg-user','reg-pwd','reg-pwd2','reg-email','reg-token'],regFunc);
	if(localStorage.getItem('remember')){
		let info=JSON.parse(localStorage.getItem('remember'));
		$('#user').val(info.user);
		$('#pwd').val(info.pwd);
		$('#remember').prop('checked',true);
	}
});

function serialInput(idArray, lastCallback)
{
	for(let i=0; i<idArray.length-1; ++i){
		$('#'+idArray[i]).on('keydown',jumpToEle(idArray[i+1]));
	}
	if(lastCallback){
		$('#'+idArray[idArray.length-1]).on('keydown',function(evt){
			if(evt.key==='Enter'){
				this.blur();
				lastCallback();
			}
		});
	}
	function jumpToEle(id)
	{
		return function(evt){
			if(evt.key==='Enter'){
				$('#'+id).focus();
			}
		};
	}
}

function changeTab(evt)
{
	var x=this,id;
	var focusMap={
		regDiv:'reg-user',
		loginDiv:'user'
	};
	$(this).parent().children().each(function(i,v){
		if(v!==x){
			id=$(v).removeClass('nav-bt-select').data('id');
			$('#'+id).css('display','none');
		} else {
			id=$(v).addClass('nav-bt-select').data('id');
			$('#'+id).removeAttr('style');
			$('#'+focusMap[id]).focus();
		}
	});
	$('#log').html('');
}

function loginFunc()
{
	let name=$('#user').val();
	let pwd=$('#pwd').val();
	let t=(Date.now()/1000|0).toString(10);
	let hash=pwd.bin('utf8').hmac_sha2('256',salt);
	let hash2,privateSalt;
	$('#login').attr('disabled',true).val('請稍後');
	$.ajax({
		url:baseUrl+'app/api/user/getSalt.php',
		method:'GET',
		data:{
			name:name
		},
		success:function(data){
			privateSalt=data;
			hash2=pwd.bin('utf8').hmac_sha2('256',data['salt']).str('hex');
			hash=hash.hmac_sha2('256',data['salt']).hmac_sha2('256',salt+t).str('hex');
			login();
		},
		error:function(){
			$('#log').text('登入失敗，請重試');
			$('#login').attr('disabled',false).val('登入');
		}
	});
	
	function login()
	{
		if($('#remember').prop('checked')){
			localStorage.setItem('remember',JSON.stringify({
				user:$('#user').val(),
				pwd:$('#pwd').val()
			}));
		} else {
			localStorage.removeItem('remember');
		}
		let data={
			name:name,
			hash:hash,
			time:t
		};
		grecaptcha.execute(
			gSiteKey,
			{action: 'login'}
		).then(function(token){
			data['gToken']=token;
			$.ajax({
				url:baseUrl+'app/api/user/login.php',
				method:'POST',
				data:data,
				success:function(data){
					sessionStorage.setItem('pwd',hash2);
					sessionStorage.setItem('pSalt',JSON.stringify(privateSalt));
					window.location.replace(location.href);
				},
				error:function(xhr){
					$('#log').text('登入失敗，請重試');
				},
				complete:function(){
					$('#login').attr('disabled',false).val('登入');
				}
			});
		});
	}
}

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
		$('#log').html('<p>'+log.join('</p><p>')+'</p>');
		return;
	}
	let hash=pwd.bin('utf8').hmac_sha2('256',salt).str('hex');
	let postData={
		token:token,
		name:user,
		hash:hash		
	}
	if(email.length>0){
		postData['email']=email;
	}
	$('#reg-run').attr('disabled',true).val('請稍後');
	$.ajax({
		url:baseUrl+'app/api/user/registry.php',
		method:'POST',
		data:postData,
		success:function(){
			$('#log').html('建立帳號成功');
			$('#nav-login').click();
		},
		error:function(){
			$('#log').html('無法建立帳號');
		},
		complete:function(){
			$('#reg-run').attr('disabled',false).val('註冊');
		}
	});
}
