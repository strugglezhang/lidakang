/*
	添加一个或几个属性值时使用
*/
function singleSave(url,data){
	$.post(url,data,function(data){
		var pjson = paseJson(data)
		if(pjson.code == "1"){

		}else{
			//alert(pjson.info)
		}
		alert(pjson.info)
	});

}