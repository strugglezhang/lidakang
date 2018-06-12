var urlIpAndPort = "http://120.76.218.161"
var baseContent = urlIpAndPort+"/smartmalls"
//员工接口地址
//增加员工地址接口
var employeeAddUrl = urlIpAndPort+"/smartmalls/Mall/Worker/worker_dml_api"
//员工列表
var employeeListUrl = urlIpAndPort+"/smartmalls/Mall/Worker/index_api"
//获取修改员工的信息
var employeeUpdateUrl = urlIpAndPort+"/smartmalls/Mall/Worker/worker_view_api"

var employeeImageUploadUrl = urlIpAndPort+"/smartmalls/Mall/Upload/worker_pic_api"
//获取地区url
/*提交参数说明：
type: province/city/district 固定三选一，默认为province
pid:对应父级ID，type为province时不需提交
返回参数说明：
code：1表示成功，2表示失败（查无相关数据！）,3 Unknowed type
date:数据列表（仅成功时才有）*/
var areaUrl = urlIpAndPort+"/smartmalls/Admin/Common/address_api"


//职位接口地址
var positionAddUrl = urlIpAndPort+"/smartmalls/Mall/Common/position_dml_api"

//部门列表接口
var departListUrl = urlIpAndPort+"/smartmalls/Mall/Common/dept_api"
//职位列表接口
var positionListUrl = urlIpAndPort+"/smartmalls/Mall/Common/position_api"
//获取页面中需要提交的数据
function htmlToJson(){
	var jsonStart = "{"
	var jsonEnd = "}"
	var jsonDelimite=","
	var items = ""
	
	$(".infoCont,.infoCont_1").each(function(){
		
		$(this).children().filter(".form-group,.form-group1").each(function(){
		
		var tag = $(this).children().filter("div").children().filter("input[type='hidden'],input[type='text'],input[type='radio'],select,textarea");
		
		tag.each(function(){
			if(typeof($(this))!='undefined' && $(this)!=null && $(this)!=''&&typeof($(this).attr("name"))!="undefined"){
			if($(this).attr("type") == "radio"){
				if($(this).attr("checked")){
					var tagName = $(this).attr("name");
					var tagValue= $(this).val();
					items += jsonItem(tagName,tagValue)+","
				}
			}else{
				//alert( $(this).attr("name")+" = "+$(this).val())
				var tagName = $(this).attr("name");
				var tagValue= $(this).val();
				items += jsonItem(tagName,tagValue)+","

			}
			
		}
		});
		
		
		})
	})
	var itemsLength = items.length
	if(itemsLength>1){
		var value = jsonStart + items.substr(0,itemsLength-1) + jsonEnd;
		return value;
	}else{
		return "";
	}
  }
function jsonItem(tagName,tagValue){
	
	if(tagValue!=null&&tagValue!=''){
		//alert('"'+tagName+'":"'+ tagValue+'"')
		return '"'+tagName+'":"'+ tagValue+'"';
	}else{
		return '"'+tagName+'":' + '""';
	}
	
}

//公用的添加方法，提交数据
// function callAdd(url,param,messagePrix){
	//alert(param)
   // var jsonData = jQuery.parseJSON(param);
  	//   $.post(url, jsonData, function(data){
   //    var jsonData = jQuery.parseJSON(data);
   //    var code = jsonData.code;
   //    var message = jsonData.info
   //    codeAnalysis(code,messagePrix,message)
   // });
// }
/*
	调用后台保存方法公用js
	url:后台添加地址
	param:需要提交的数据
	messagePrix:消息前缀
*/
function callAdd(url,param,messagePrix){
	//alert(param)
   var jsonData = jQuery.parseJSON(param);
  	  $.post(url, jsonData, function(data){
      var jsonData = jQuery.parseJSON(data);
      var code = jsonData.code;
      var message = jsonData.info
      codeAnalysis(code,messagePrix,message)
   });
}
//解析返回结果
function codeAnalysis(code,messagePrix,message){
	if( code == "1"){
      	alert(messagePrix+"成功")
      	return
      }else if(code == "2"){
      	alert(messagePrix+"失败！非法操作！"+message)

      	return 
      }else{
      	alert(code)
      	alert(message)
      	return
      }
}

//加载列表公用方法
//target table body 的id
//获取列表信息的url
//请求参数
//columnNames展示的列，展昭table的字段顺序,如：aa:src,bb:href,cc:input,dd:td;id每行的主键
//specialColumn：特殊列内容;first : 特殊列位置true 一个td，最后一个td
function listInsert(target,url,param,columnNames,isSelected,id,specialColumn,first,pageProgationId,isPage){
	var paramData = paseJson(param)
	$.post(url, paramData, function(data){
      var jsonData = jQuery.parseJSON(data);
      var code = jsonData.code
	  var columns = columnNames.split(",")
      var html = ""
      
      if(code=='1'){
		var employeeData = jsonData.data
		$.each(employeeData,function(k,v){
			var str = "<tr>"
			var etr = "</tr>"
			var td = ""

			if(isSelected){
				td = td +'<td class="checkbox-column  sorting_1"><input type="checkbox" value="'+v[id]+'"></td>'
			}
			
			if(specialColumn !=null && first ){
				var re = new RegExp("IDVALUE","g");
				td = td + specialColumn.replace(re,v[id])

			}
			$.each(columns,function(k,column){
				var tag = column.split(":")
				var value=v[tag[0]]
				if(tag[1] == "image"){
					td = td+'<td><image src="'+(value)+'"></td>'
				}else if (tag[1] == "href"){
					td = td+'<td><href src="'+(value)+'"></td>'
				}else{
					td = td+'<td>'+(value)+'</td>'
				}
				
			});
			if(specialColumn !=null && !first){
				var re = new RegExp("IDVALUE","g");
				td = td + specialColumn.replace(re,v[id])
			}
			html = html +(str+td+etr)

		});
		$("#"+target).empty()
		$("#"+target).html(html);
		var page_count = jsonData.page_count
		if(isPage){
			$("#"+pageProgationId).empty()
            $("#"+pageProgationId).append('<div class="jump_to">跳转到<input type="text" class="click_jump">页<input type="button" id="jump2"class="tj" value="点击">共 <b>'+page_count+'</b>页</div>')
			$("#"+pageProgationId).append('<button class="btn btn-sm">下一页</button>')
			for(var i=1;i<=page_count;i++){
				$("#"+pageProgationId).append('<a href="javascript:void();">'+i+'</a>')
			}
			$("#"+pageProgationId).append('<button class="btn btn-sm">上一页</button>')

			$("#pageProgation").find("a").on("click",function(){
		      var page = $(this).html();
		      pageProgation(employeeListUrl,page,'keyword,dept_id,state')
		      $(this).addClass("page_on");
		      $(this).siblings().removeClass("page_on");
		    });
		    $("#jump2").on("click",function(){
			   var page = $(".click_jump").val();
			   pageProgation(employeeListUrl,page,'keyword,dept_id,state')
			}); 
		}
      }else{
		  alert("获取数据失败")
	  }
   });
}
/*分页公用函数*/

function pageProgation(url,page,columns){

  /*var param = '{"pagesize":PAGESIZE,"page":PAGE,"keyword":KEYWORK,"dept_id":DEPARTID,"state":STATE}'
  var keyword= '"'+$("#keyword").val()+'"';
  var dept_id = '"'+$("#dept_id").val()+'"'
  var state = '"'+$("#state").val()+'"'*/
  var params = "";
  var column = columns.split(",")
  $.each(column,function(k,v){
  	params = params + '"'+v+'":"'+$("#"+v).val()+'",';
  });
  params ='{"pagesize":10,"page":'+page+','+params.substr(0,params.length-1)+'}'
  //param = param.replace("PAGESIZE",5).replace("PAGE",page).replace("KEYWORK",keyword).replace("DEPARTID",dept_id).replace("STATE",state)
  listInsert("employeeList",url,params,"name:td,number:td,address:td,dept_name:td,position_name:td,sex:td,birthday:td,phone:td",true,"id","<td><a href='employeeinfo.html'>查看</a><a href='addEmployee.html?type=update&worker_id=IDVALUE'>修改</a><a href=\"javascript:;\"  id=\"tab_1_2\"   data-toggle=\"modal\" data-target=\"#DeBrand\">删除</a></td>",false,'pageProgation',true)
}
//获取修改的信息
//url 请求地址，参数,需要显示的列名列名必须要和页面字段名称相同
function getUpdateData(url,param,columnNames){
	var paramData = paseJson(param)

	$.post(url, param, function(data){
      var jsonData = jQuery.parseJSON(data);
      var code = jsonData.code
	  var columns = columnNames.split(",")
      var html = ""
      if(code=='1'){
		var valueData = jsonData.data
		$.each(columns,function(k1,column){
			var cl = column.split(":")
			var v = valueData[cl[0]]
			if(cl[1] == "select"){
				var flage = true
				$("#"+cl[0]+" option").each(function(){
					if($(this).val() == v && flage){
						
						$(this).attr("selected",true)
						flage = false;
					}
				});
			}else if(cl[1] == "textarea"){

				$("input[name='"+cl[0]+"']").val(v)
			}else if(cl[1] == "image"){
				//alert(v)
				$(".saw_file").css("background-image",'url("'+v+'")');
			}else{
				//alert(cl[0] + " = "+ v)
				$("input[name='"+cl[0]+"']").val(v)
			}
		});
		
      }else{
		  alert(jsonData.info)
	  }
   });
}
function areaInit(privenceTag,cityTag,districtTag){
	var pid = codeAnalysisArea(privenceTag,'province','0')
}
/*$("#birth_province").on("change",function(){
    alert($(this).val())
});*/
/*解析并设置地区*/
function codeAnalysisArea(target,type,pid){
	//获取省份

	$.post(areaUrl,{'type':type,'pid':pid},function(data){

		var pjson = paseJson(data)
		var code = pjson.code
		
		var message = pjson.info
		if(code=="1"){
			var data = pjson.data
			var options = "";
			var firstId = 0;
			$.each(data,function(k,v){
							
				if(k == 0){
					firstId = v.id
					options = options+"<option value='"+v.id+"'selected=true >"+v.name+"</option>"
				}else{
					options = options+"<option value='"+v.id+"'>"+v.name+"</option>"
				}

			});
			$(target).empty();
			$("#"+target).html(options)
			$.post(areaUrl,{'type':'city','pid':firstId},function(data){
				var pjson = paseJson(data)
				var code = pjson.code
				
				var message = pjson.info
				options = "";
				if(code=="1"){
					var data = pjson.data
					var options = "";
					var firstId = 0;
					$.each(data,function(k,v){
						if(k == 0){
							firstId = v.id
							options = options+"<option value='"+v.id+"'selected=true >"+v.name+"</option>"
						}else{
							options = options+"<option value='"+v.id+"'>"+v.name+"</option>"
						}

					});
					$("#birth_city").empty();
					$("#birth_city").html(options)

					$.post(areaUrl,{'type':'district','pid':firstId},function(data){
						var pjson = paseJson(data)
						var code = pjson.code
						
						var message = pjson.info
						options = "";
						if(code=="1"){
							var data = pjson.data
							var options = "";
							var firstId = 0;
							$.each(data,function(k,v){
								if(k == 0){
									firstId = v.id
									options = options+"<option value='"+v.id+"'selected=true >"+v.name+"</option>"
								}else{
									options = options+"<option value='"+v.id+"'>"+v.name+"</option>"
								}

							});
							$("#birth_district").empty();
							$("#birth_district").html(options)
							return firstId
						}else if(code="2"){
							sysMessage(message)
							return 0;
						}else{
							sysMessage(message)
							return 0;
						}
					});
				}else if(code="2"){
					sysMessage(message)
					return 0;
				}else{
					sysMessage(message)
					return 0;
				}
			});
		}else if(code="2"){
			sysMessage(message)
			return 0;
		}else{
			sysMessage(message)
			return 0;
		}
	});
}

function selectPrivence(target,type,pid){
	//获取省份
	$.post(areaUrl,{'type':'city','pid':pid},function(data){
		var pjson = paseJson(data)
		var code = pjson.code
		var message = pjson.info
		options = "";
		if(code=="1"){
			var data = pjson.data
			var options = "";
			var firstId = 0;
			$.each(data,function(k,v){
				
				if(k == 0){
					firstId = v.id
				}

				if(v.id == pid){
					options = options+"<option value='"+v.id+"'selected=true>"+v.name+"</option>"
					// alert(options)
					

				}else{
					options = options+"<option value='"+v.id+"'>"+v.name+"</option>"
				}

			});
			$("#birth_city").empty();

			$("#birth_city").html(options)

			$.post(areaUrl,{'type':'district','pid':firstId},function(data){
				var pjson = paseJson(data)
				var code = pjson.code
				
				var message = pjson.info
				options = "";
				if(code=="1"){
					var data = pjson.data
					var options = "";
					var firstId = 0;
					$.each(data,function(k,v){
						options = options+"<option value='"+v.id+"'>"+v.name+"</option>"
						if(k == 0){
							firstId = v.id
						}

					});
					$("#birth_district").empty();
					$("#birth_district").html(options)
					return firstId
				}else if(code="2"){
					sysMessage(message)
					return 0;
				}else{
					sysMessage(message)
					return 0;
				}
			});
		}else if(code="2"){
			sysMessage(message)
			return 0;
		}else{
			sysMessage(message)
			return 0;
		}
	});
}
function codeAnalysisCity(target,type,pid){
	//获取省份
	$.post(areaUrl,{'type':'district','pid':pid},function(data){
		var pjson = paseJson(data)
		var code = pjson.code
		
		var message = pjson.info
		options = "";
		if(code=="1"){
			var data = pjson.data
			var options = "";
			var firstId = 0;
			$.each(data,function(k,v){
				options = options+"<option value='"+v.id+"'>"+v.name+"</option>"
				if(k == 0){
					firstId = v.id
				}

			});
			$("#birth_district").empty();
			$("#birth_district").html(options)
			return firstId
		}else if(code="2"){
			sysMessage(message)
			return 0;
		}else{
			sysMessage(message)
			return 0;
		}
	});
}
/*
	两级级联操作
*/
function seconeLevelRelationAction(firstUrl,secondUrl,firstTargetName,secondTargetName,param,pid,firstId1,firstName,secondId,secondName){
	$.post(firstUrl,param,function(data){
		var jData = paseJson(data);
		if(jData.code=="1"){
			var nowData = jData.data
			var options = ""
			var firstId = 0;
			$.each(nowData,function(k,v){
				options = options+"<option value='"+v[firstId1]+"'>"+v[firstName]+"</option>"
				if(k == 0){
					firstId = v.id
				}

			});
			$("#"+firstTargetName).empty();
			$("#"+firstTargetName).html(options)
			var pParam = '{'+pid+'"'+firstId+'"'+'}'
			options = ""
			pParam = paseJson(pParam)
			$.post(secondUrl,pParam,function(sdata){
				var seData = paseJson(sdata)
				if(seData.code=="1"){
					var snowData = seData.data
					$.each(snowData,function(k,v){
						options = options+"<option value='"+v[secondId]+"'>"+v[secondName]+"</option>"
					});
					$("#"+secondTargetName).empty();
					$("#"+secondTargetName).html(options)

				}else{
					sysMessage(seData.info)
				}
			});
		}else{
			sysMessage(jData.info)
		}

	});
}
/*
	两级级联是，选择第一级时，更新第二级的值
*/
function seconeLevelSelectAction(url,param,targetName,id,name,isClear){
	var jParam = paseJson(param)
	$.post(url,jParam,function(data){
		var nowData = paseJson(data)
		if(nowData.code=="1"){
			var jsonData = nowData.data
			var options = ""
			$.each(jsonData,function(k,v){
				options = options+"<option value='"+v[id]+"'>"+v[name]+"</option>"
			});
			if(isClear){
				alert(1)
				$("#"+targetName).empty();
			}
			
			$("#"+targetName).append(options)
		}else{
			sysMessage(nowData.info)
			$("#"+targetName).empty();
			$("#"+targetName).html("<option value='"+"a"+"'>"+""+"</option>")
		}
	});
}

/*
	获取下拉框数据公用方法
	url:地址
	param:提交的参数
	targetName：selectId
	id：下拉框value的值域
	name:下拉框显示域
*/
function selectQuery(url,param,targetName,id,name){
	var paramData = paseJson(param)
	$.post(url,paramData,function(data){
		var value = paseJson(data)
		var code = value.code
		var info = value.info
		if(code == "1"){
			var vdata = value.data
			var options ="";
			$.each(vdata,function(k,v){
				options = options + '<option value="'+v[id]+'">'+v[name]+'</option>'
			});

			$("#"+targetName).empty();
			$("#"+targetName).html(options)
		}else{
			sysMessage(info)
		}
	});

}
//解析并设置省份信息
function setAreaDate(target,data){
	
}
//公用json格式化
function paseJson(str){
	return jQuery.parseJSON(str);
}
//公用提示框
function sysMessage(message){
	alert(message);
}

//提交前校验
function beforeRequest(){
	return true;
}

//头像上传
//
/*$('#one-specific-file').ajaxfileupload({
    'action': 'http://120.76.218.161/smartmalls/Mall/Worker/index_api'
});*/
$(document).ready(function(){
    var num = 0;
    $('#uploadfile').change (function(){
        num++;
	  $('.saw_file').css('backgroundImage' ,'url('+window.URL.createObjectURL(this.files[0])+')');//图片路径
    });
    $('#submitImg').click(function(){
        if(num ==0){
            alert('请添加图片');
        }else{
            ajaxFileUpload();
        }
    });
    function ajaxFileUpload() {
        $.ajaxFileUpload(
            {
                /*http://120.76.218.161*/
                type:"post",
                url: employeeImageUploadUrl,
                secureuri: false,
                fileElementId: 'uploadfile',
                dataType: 'json',
                success: function (data, status)
                {
                	//alert("data"+data.url)
                	
                    if(data.code == 1){
                    	$.cookie("employeePic",data.url)
                    	$("#pic").val(baseContent+$.cookie("employeePic"))
                        alert("提交成功！");
                    }else{
                        alert(data.info);
                    }
                },
                error: function (data, status, e)
                {
                    sysMessage("上传失败")
//                            console.log(data);
                }
            }
        )
        //return false;
    }

     /*function ajaxFileUpload() {
    	var file = $("#uploadfile").get(0).files[0];
		var fileData = new FormData();
		fileData.append("file", file);
		EasyAjax.upload_file({
                    url: employeeImageUploadUrl,
                    data: fileData,
                    type:"post",
                    dataType:"json",
                    contentType:"multipart/form-data",
                    timeout:30,
                    async:true,
                    cache:false,
                    processData:true
                },
                function (data) {
                    if (data.success) {
                        //上传成功
                    }
                });
        return false;
    }*/
});
