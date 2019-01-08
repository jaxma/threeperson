function challs_flash_update(){ //Flash 初始化函数
	var a={};
	//定义变量为Object 类型

	a.title = "上传文件"; //设置组件头部名称
	
	a.FormName = "Filedata";
	//设置Form表单的文本域的Name属性
	
	a.url="admin.php/Video/batchImage"; 
	//设置服务器接收代码文件
	
	a.parameter="bs=tyi&id=50"; 
	//设置提交参数，以GET形式提交
	
	a.typefile=["Images (*.gif,*.png,*.jpg,*jpeg)","*.gif;*.png;*.jpg;*.jpeg;","GIF (*.gif)","*.gif;","PNG (*.png)","*.png;","JPEG (*.jpg,*.jpeg)","*.jpg;*.jpeg;"];
	//设置可以上传文件 数组类型
	//"Images (*.gif,*.png,*.jpg)"为用户选择要上载的文件时可以看到的描述字符串,
	//"*.gif;*.png;*.jpg"为文件扩展名列表，其中列出用户选择要上载的文件时可以看到的 Windows 文件格式，以分号相隔
	//2个为一组，可以设置多组文件类型
	
	a.UpSize=0;
	//可限制传输文件总容量，0或负数为不限制，单位MB
	
	a.fileNum=0;
	//可限制待传文件的数量，0或负数为不限制
	
	a.size=2;
	//上传单个文件限制大小，单位MB，可以填写小数类型
	
	a.FormID=['goods_ida'];
	//设置每次上传时将注册了ID的表单数据以POST形式发送到服务器
	//需要设置的FORM表单中checkbox,text,textarea,radio,select项目的ID值,radio组只需要一个设置ID即可
	//参数为数组类型，注意使用此参数必须有 challs_flash_FormData() 函数支持
	
	a.autoClose=1;
	//上传完成条目，将自动删除已完成的条目，值为延迟时间，以秒为单位，当值为 -1 时不会自动关闭，注意：当参数CompleteClose为false时无效
	
	a.CompleteClose=true;
	//设置为true时，上传完成的条目，将也可以取消删除条目，这样参数 UpSize 将失效, 默认为false
	
	a.repeatFile=true;
	//设置为true时，可以过滤用户已经选择的重复文件，否则可以让用户多次选择上传同一个文件，默认为false
	
	a.returnServer=true;
	//设置为true时，组件必须等到服务器有反馈值了才会进行下一个步骤，否则不会等待服务器返回值，直接进行下一步骤，默认为false
	
	a.MD5File = 1;
	//设置MD5文件签名模式，参数如下 ,注意：FLASH无法计算超过100M的文件,在无特殊需要时，请设置为0
	//0为关闭MD5计算签名
	//1为直接计算MD5签名后上传
	//2为计算签名，将签名提交服务器验证，在根据服务器反馈来执行上传或不上传
	//3为先提交文件基本信息，根据服务器反馈，执行MD5签名计算或直接上传，如果是要进行MD5计算，计算后，提交计算结果，在根据服务器反馈，来执行是否上传或不上传
	
	a.loadFileOrder=true;
	//选择的文件加载文件列表顺序，TRUE = 正序加载，FALSE = 倒序加载
	
	a.mixFileNum=0;
	//至少选择的文件数量，设置这个将限制文件列表最少正常数量（包括等待上传和已经上传）为设置的数量，才能点击上传，0为不限制
	
	a.ListShowType = 1;
	//文件列表显示类型：1 = 传统列表显示，2 = 缩略图列表显示（适用于图片专用上传）
	
	a.InfoDownRight = "等待上传：%1%个  已上传：%2%个";
	//右下角统计信息的文本设置,文本中的 %1% = 等待上传数量的替换符号，%2% = 已经上传数量的替换符号
	
	a.TitleSwitch = true;
	//是否显示组件头部
	
	a.ForceFileNum=0;
	//强制条目数量，已上传和待上传条目相加等于为设置的值（不包括上传失败的条目），否则不让上传, 0为不限制，设置限制后mixFileNum,autoClose和fileNum属性将无效！
	
	a.autoUpload = false;
	//设置为true时，用户选择文件后，直接开始上传，无需点击上传，默认为false;
	
	a.adjustOrder = true;
	//设置为true时，用户可以拖动列表，重新排列位置
	
	a.deleteAllShow = true;
	//设置是否显示，全部清除按钮
	
	return a ;
	//返回Object
}

function challs_flash_onComplete(a){ //每次上传完成调用的函数，并传入一个Object类型变量，包括刚上传文件的大小，名称，上传所用时间,文件类型
	var name=a.fileName; //获取上传文件名
	var size=a.fileSize; //获取上传文件大小，单位字节
	var time=a.updateTime; //获取上传所用时间 单位毫秒
	var type=a.fileType; //获取文件类型，在 Windows 上，此属性是文件扩展名。 在 Macintosh 上，此属性是由四个字符组成的文件类型
	document.getElementById('show').innerHTML+="";
}

function challs_flash_onCompleteData(a){ //获取服务器反馈信息事件
	document.getElementById('show').innerHTML+=a+'';	
}
function challs_flash_onStart(a){ //开始一个新的文件上传时事件,并传入一个Object类型变量，包括刚上传文件的大小，名称，类型
	var name=a.fileName; //获取上传文件名
	var size=a.fileSize; //获取上传文件大小，单位字节
	var type=a.fileType; //获取文件类型，在 Windows 上，此属性是文件扩展名。 在 Macintosh 上，此属性是由四个字符组成的文件类型
	document.getElementById('show').innerHTML+="";
	
	return true; //返回 false 时，组件将会停止上传
}

function challs_flash_onCompleteAll(a){ //上传文件列表全部上传完毕事件,参数 a 数值类型，返回上传失败的数量
	document.getElementById('show').innerHTML+='<font color="#ff0000">所有文件上传完毕，</font>上传失败'+a+'个！<br />';
	//window.location.href='http://www.access2008.cn/update'; //传输完成后，跳转页面
}

function challs_flash_deleteAllFiles(){ //清空按钮点击时，出发事件

	//返回 true 清空，false 不清空
	return confirm("你确定要清空列表吗?");
}

function challs_flash_onError(a){ //上传文件发生错误事件，并传入一个Object类型变量，包括错误文件的大小，名称，类型
	var err=a.textErr; //错误信息
	var name=a.fileName; //获取上传文件名
	var size=a.fileSize; //获取上传文件大小，单位字节
	var type=a.fileType; //获取文件类型，在 Windows 上，此属性是文件扩展名。 在 Macintosh 上，此属性是由四个字符组成的文件类型
	document.getElementById('show').innerHTML+='<font color="#ff0000">'+name+' - '+err+'</font><br />';
}

function challs_flash_FormData(a){ // 使用FormID参数时必要函数
	try{
		var value = '';
		var id=document.getElementById(a);
		if(id.type == 'radio'){
			var name = document.getElementsByName(id.name);
			for(var i = 0;i<name.length;i++){
				if(name[i].checked){
					value = name[i].value;
				}
			}
		}else if(id.type == 'checkbox'){
			var name = document.getElementsByName(id.name);
			for(var i = 0;i<name.length;i++){
				if(name[i].checked){
					if(i>0) value+=",";
					value += name[i].value;
				}
			}
		}else{
			value = id.value;
		}
		return value;
	 }catch(e){
		return '';
	 }
}

function challs_flash_style(){ //组件颜色样式设置函数
	var a = {};
	
	/*  整体背景颜色样式 */
	a.backgroundColor=['#f6f6f6','#f3f8fd','#dbe5f1'];	//颜色设置，3个颜色之间过度
	a.backgroundLineColor='#5576b8';					//组件外边框线颜色
	a.backgroundFontColor='#066AD1';					//组件最下面的文字颜色
	a.backgroundInsideColor='#FFFFFF';					//组件内框背景颜色
	a.backgroundInsideLineColor=['#e5edf5','#34629e'];	//组件内框线颜色，2个颜色之间过度
	a.upBackgroundColor='#ffffff';						//上翻按钮背景颜色设置
	a.upOutColor='#000000';								//上翻按钮箭头鼠标离开时颜色设置
	a.upOverColor='#FF0000';							//上翻按钮箭头鼠标移动上去颜色设置
	a.downBackgroundColor='#ffffff';					//下翻按钮背景颜色设置
	a.downOutColor='#000000';							//下翻按钮箭头鼠标离开时颜色设置
	a.downOverColor='#FF0000';							//下翻按钮箭头鼠标移动上去时颜色设置
	
	/*  头部颜色样式 */
	a.Top_backgroundColor=['#e0eaf4','#bcd1ea']; 		//颜色设置，数组类型，2个颜色之间过度
	a.Top_fontColor='#245891';							//头部文字颜色
	
	
	/*  按钮颜色样式 */
	a.button_overColor=['#FBDAB5','#f3840d'];			//鼠标移上去时的背景颜色，2个颜色之间过度
	a.button_overLineColor='#e77702';					//鼠标移上去时的边框颜色
	a.button_overFontColor='#ffffff';					//鼠标移上去时的文字颜色
	a.button_outColor=['#ffffff','#dde8fe']; 			//鼠标离开时的背景颜色，2个颜色之间过度
	a.button_outLineColor='#91bdef';					//鼠标离开时的边框颜色
	a.button_outFontColor='#245891';					//鼠标离开时的文字颜色
	
	/* 文件列表样式 */
	a.List_backgroundColor='#EAF0F8';					//列表背景色
	a.List_fontColor='#333333';							//列表文字颜色
	a.List_LineColor='#B3CDF1';							//列表分割线颜色
	a.List_cancelOverFontColor='#ff0000';				//列表取消文字移上去时颜色
	a.List_cancelOutFontColor='#D76500';				//列表取消文字离开时颜色
	a.List_progressBarLineColor='#B3CDF1';				//进度条边框线颜色
	a.List_progressBarBackgroundColor='#D8E6F7';		//进度条背景颜色	
	a.List_progressBarColor=['#FFCC00','#FFFF00'];		//进度条进度颜色，2个颜色之间过度
	
	/* 错误提示框样式 */
	a.Err_backgroundColor='#C0D3EB';					//提示框背景色
	a.Err_LineColor='#5D7CBB';							//提示框边框线景色
	a.Err_cancelOverColor='#0066CC';					//提示框取消按钮移上去时颜色
	a.Err_cancelOutColor='#FF0000';						//提示框取消按钮离开时颜色
	a.Err_fontColor='#245891';							//提示框文字颜色
	
	
	return a;
}


var isMSIE = (navigator.appName == "Microsoft Internet Explorer");   
function thisMovie(movieName){   
  if(isMSIE){   
  	return window[movieName];   
  }else{
  	return document[movieName];   
  }   
}