	//用于压缩图片的canvas 
	var canvas = document.createElement("canvas"); 
	var ctx = canvas.getContext('2d'); 
	
	 //    瓦片canvas 
	var tCanvas = document.createElement("canvas"); 
	
	var tctx = tCanvas.getContext("2d"); 
    var maxsize = 500 * 1024;
	/////////////////
	
	var result = document.getElementById("list1");
    var input = document.getElementById("myfile1");

    if(typeof FileReader === 'undefined'){
        result.innerHTML = "<p class='warn'>抱歉，你的浏览器不支持 FileReader</p>";
        input.setAttribute('disabled','disabled');
    }else{
        input.addEventListener('change',readFile,false);
    }
    
    function readFile(){  
        for(var i=0;i<this.files.length;i++)
        {
            var file = this.files[i];
            if(!/image\/\w+/.test(file.type)){
                alert("请确保文件为图像类型");
                return false;
            }
            // check for file size
            if (file.size > 1024 * 1024 * 10) {
                alert('图片大小不能超过10M'); 
                return false;
            }
            /////////
            var Orientation = null;  
            // var URL = URL || webkitURL;  
            //获取照片方向角属性，用户旋转控制  
            EXIF.getData(file, function() {  
            // alert(EXIF.pretty(this));  
            EXIF.getAllTags(file);   
            //alert(EXIF.getTag(this, 'Orientation'));   
            Orientation = EXIF.getTag(file, 'Orientation');
            //return;  
            });
            var reader = new FileReader();
            reader.readAsDataURL(file);// console.log(this);                    
            reader.onload = function(e){  
                result.innerHTML= '<img src="'+this.result+'" onclick="del_pic('+this+')" alt=""/>';                     
                var data = "";
                //如果图片大小小于500kb，则直接上传 
                if (file.size <= maxsize) {
                     data = this.result;
                    $.post(uploadUrl,{imgData:data,Orientation:Orientation,type:"payUpload"},function(data){
                        $("#flimg").val(data);
                    });
                 }else{
                    var myImg = new Image(); 
                    myImg.src = this.result;
                    myImg.onload=function(){
                        data = compress(myImg);
                        //alert(data);
                        $.post(uploadUrl,{imgData:data,Orientation:Orientation,type:"payUpload"},function(data){
                            $("#flimg").val(data);
                        });
                    }
                }
            }
        }
    }
    ///////////
    function compress(img) {
        var initSize = img.src.length; 
        var height = img.naturalHeight; 
        var width = img.naturalWidth; 
        //alert(width);
        //alert(height);

        //如果图片大于四百万像素，计算压缩比并将大小压至400万以下 
        var ratio; 
        if ((ratio = width * height / 4000000)>1) { 
         ratio = Math.sqrt(ratio); 
         width /= ratio; 
         height /= ratio; 
        }else { 
         ratio = 1; 
        } 

        canvas.width = width; 
        canvas.height = height; 

        //        铺底色 
        ctx.fillStyle = "#fff"; 
        ctx.fillRect(0, 0, canvas.width, canvas.height); 

        //如果图片像素大于100万则使用瓦片绘制 
        var count; 
        if ((count = width * height / 1000000) > 1) { 
         count = ~~(Math.sqrt(count)+1); //计算要分成多少块瓦片 

        //            计算每块瓦片的宽和高 
         var nw = ~~(width / count); 
         var nh = ~~(height / count); 

         tCanvas.width = nw; 
         tCanvas.height = nh; 

         for (var i = 0; i < count; i++) { 
             for (var j = 0; j < count; j++) { 
                 tctx.drawImage(img, i * nw * ratio, j * nh * ratio, nw * ratio, nh * ratio, 0, 0, nw, nh); 

                 ctx.drawImage(tCanvas, i * nw, j * nh, nw, nh); 
             } 
         } 
        } else { 
         ctx.drawImage(img, 0, 0, width, height); 
        } 

        //进行最小压缩 
        var ndata = canvas.toDataURL('image/jpeg', 0.1); 

        /*console.log('压缩前：' + initSize); 
        console.log('压缩后：' + ndata.length); 
        console.log('压缩率：' + ~~(100 * (initSize - ndata.length) / initSize) + "%"); */

        tCanvas.width = tCanvas.height = canvas.width = canvas.height = 0; 

        return ndata; 
    }

