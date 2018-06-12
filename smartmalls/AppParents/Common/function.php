<?php

function randCode()
{
    $code = '';
    for($i =0; $i<4;$i++){
        $code .= rand(0,9);
    }
    return $code;
}

function curlData($code,$phone)
{
    $host = "http://smsapi.api51.cn";
    $path = "/code/";
    $method = "GET";
    $appcode = "c43a7bbd0eed4ef6a088fafb683aa302";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "code=$code&mobile=$phone";
    $url = $host . $path . "?" . $querys;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $result = curl_exec($curl);
    return $result;

}



 function getMember($member_id){
    if($member_id){
        return D('Member')->where('id='.$member_id)->field('name,phone')->find();
    }
}
 function getCourse($course_id){
    if($course_id){
        return D('CoursePlan')->where('id='.$course_id)->field('id,start_time,end_time,room_number')->find();
    }
}

function getCourseTryout(){
    return D('CoursePlan')->find();
}

function getMemberCard($memeber_id){
    if($memeber_id){
        return D('Member')->where('id='.$memeber_id)->field('id,balance,cardnumber_no,card_number,name')->select();
    }
}
function getCard($card_id){
    if($card_id){
        return D('CourseCard')->where('id='.$card_id)->field('course_price,validity_ttimes,gifts,all_count')->select();
    }
}
function getCardByCardNo($card_number)
{
     if($card_number){
         return D('Card')->where('card_number='.$card_number)->field('id,card_typeid,card_ownewneme,card_number,cardnumber_no,card_state,card_ownerid')->select();
     }
}
function updateMemberBalance($res){
    return D('Member')->save($res);
}
function addFees($cads){
    return D('ExpenseDetail')->add($cads);
}
function addReserve($cads){
    return D('CourseOrder')->add($cads);
}

function addMall($mallinfo){
    return D('MallRevenue')->add($mallinfo);
}

function getCardInfo($course_id){
    if($course_id){
        return D('CourseCard')->where('id='.$course_id)->field('course_price')->select();
    }
}
function getReserve($member_id){
    if($member_id){
        return D('CourseOrder')->where('member_id='.$member_id)->field('id,used_times,total_degree')->select();
    }
}

function updateReserve($res){
    return D('CourseOrder')->save($res);
}

function getCoursePic($course_id){
    if(!$course_id){
        return false;
    }
    return D('Course')->where('id='.$course_id)->field('name,pic')->select();
}


