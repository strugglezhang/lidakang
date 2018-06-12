/**
 * Created with JetBrains WebStorm.
 * User: zhaoyang
 * Date: 14-1-17
 * Time: 下午2:31
 * To change this template use File | Settings | File Templates.
 */
$(function(){
    $("form :input").each(function(){
        $(this).blur(function(){
            // 验证为空,字符长度
            // $('input[id^=use_]').blur(function(){
            //     var $parent = $(this).parents(".form-group");
            //     var $Msg = $(this).next('.help-block');
            //     if($(this).val() =="" || $(this).val().length < 6){
            //         $parent.removeClass('has-success');
            //         $parent.addClass('has-error');
            //         $Msg.html('请输入至少6位数的用户名');
            //     }else{
            //         $parent.removeClass('has-error');
            //         $parent.addClass('has-success');
            //         $Msg.html('用户名正确');
            //     }
            // });
            // 必填字段
            $('input[id^=Required_],select[id^=Required_]').blur(function(){
                var $parent = $(this).parents(".form-group");
                var $Msg = $(this).next('.help-block');
                if($(this).val() =="" || $(this).val().length < 1){
                    $parent.removeClass('has-success');
                    $parent.addClass('has-error');
                    $Msg.html('该字段不能为空');
                }else{
                    $parent.removeClass('has-error');
                    $parent.addClass('has-success');
                    $Msg.html('用户名正确');
                }
            });

            // 验证邮箱格式
            $('input[id^=mail_]').blur(function(){
                var $parent = $(this).parents(".form-group");
                var $Msg = $(this).next('.help-block');
                if(this.value=="" || (this.value!="" && !/.+@.+\.[a-zA-z]{2,4}$/.test(this.value))){
                    $parent.removeClass('has-success');
                    $parent.addClass('has-error');
                    $Msg.html('请输入正确的E-Mail 地址');
                }else{
                    $parent.removeClass('has-error');
                    $parent.addClass('has-success');
                    $Msg.html('E-Mail地址正确');
                }
            });
       
            // 验证是否为数字
            $('input[id^=number_]').blur(function(){
                var $parent = $(this).parents(".form-group");
                var $Msg = $(this).next('.help-block');
                if(!$.isNumeric(this.value)){
                    $parent.removeClass('has-success');
                    $parent.addClass('has-error');
                    $Msg.html('请输入数字');
                }else{
                    $parent.removeClass('has-error');
                    $parent.addClass('has-success');
                    $Msg.html('输入正确');
                }
            });
            // 验证是否是电话号码
            $('input[id^=numberphone_]').blur(function(){
                var $parent = $(this).parents(".form-group");
                var $Msg = $(this).next('.help-block');
                 if(this.value=="" || (this.value!="" && !/^1[3|4|5|8][0-9]\d{4,8}$/.test(this.value)) || this.value.length < 11){
                    $parent.removeClass('has-success');
                    $parent.addClass('has-error');
                    $Msg.html('请输入电话号码');
                }else{
                    $parent.removeClass('has-error');
                    $parent.addClass('has-success');
                    $Msg.html('输入正确');
                }
            });
             // 验证身份证号码
             $('input[id^=IDCard_]').blur(function(){
                var $parent = $(this).parents(".form-group");
                var $Msg = $(this).next('.help-block');
                if(this.value=="" || (this.value!="" && !/^[1-9]{1}[0-9]{14}$|^[1-9]{1}[0-9]{16}([0-9]|[xX])$/.test(this.value)) || this.value.length < 18){
                    $parent.removeClass('has-success'); 
                    $parent.addClass('has-error');
                    $Msg.html('请输入正确的身份证号码');
                }else{
                    $parent.removeClass('has-error');
                    $parent.addClass('has-success');
                    $Msg.html('身份证号码正确');
                }
            });
            }).keyup(function(){
                    $(this).triggerHandler("blur");
                }).focus(function(){
                    $(this).triggerHandler("blur");
                });
    });
    $("#addbtn").click(function(){
        var $selProvince1 = $("#selProvince1").val().substring(7);
        var $selCity1 = $("#selCity1").val().substring(7);
        var $selDistrict1 = $("#selDistrict1").val().substring(7);
        var $city = $selProvince1 + $selCity1 + $selDistrict1;
        $(this).parent(".col-xs-5").next(".col-xs-5").append("<span class='label label-success marl5 font12'>" + $city + "<a class='deleate' href='javascript:;'>×</a></span>");
        $("#area .deleate").click(function(){
            $(this).parent().remove();
        });
    });
//  修改企业资料
    $("#ChangeBtn").click(function(){
        $("#ChangeTxt").fadeIn();
    });
//消费者管理--展开高级搜索
    $("#Adsearch").click(function(){
        $(this).toggleClass("Tfade");
        $(this).next(".Ad-search-in").fadeToggle();
    });
//全选，反选 取消
    $("#CheckedAll").click(function(){
        $('[name=items]:checkbox').attr('checked',true);
    });
    $("#CheckedNo").click(function(){
        $('[name=items]:checkbox').attr('checked',false);
    });
    $("#CheckedRev").click(function(){
        $('[name=items]:checkbox').each(function(){
            this.checked =!this.checked;
        });
    });
//任务收益设置 弹出框修改名称
    $("#a_re").click(function(){
        $(this).parent("#Rset").addClass("current");
    });
    $("#a_ok").click(function(){
        var $this = $(this).prevAll("input");
		var regxThis = /.*[^ ].*/;
        var $Rname =$(this).prevAll("span");
        var $Rval = $(this).prevAll("input").val();
        $(this).parent("#Rset").removeClass("current");
        if(regxThis.test($this.val())){
            $Rname.html($Rval);
        }
        $(this).prevAll("input").val("");
    });
});














