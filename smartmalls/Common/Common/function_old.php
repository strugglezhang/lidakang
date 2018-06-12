<?php
function checkLogin($type=1){
	switch ($type) {
		case 'admin':
		case 1:
			if (!isset($_SESSION['admin'])) {
				echo json_encode(array('status' => 0));
				exit;
			}
			break;
		case 'parent':
		case 2:
			if (!isset($_SESSION['parent'])) {
				echo json_encode(array('status' => 0));
				exit;
			}
			break;
		case 'manager':
		case 3:
			if (!isset($_SESSION['manager'])) {
				echo json_encode(array('status' => 0));
				exit;
			}
			break;
		case 'teacher':
		case 4:
			if (!isset($_SESSION['admin']) || $_SESSION['admin']['position'] != 3) {
				echo json_encode(array('status' => 0));
				exit;
			}
			break;
		case 'leader':
		case 5:
			if (!isset($_SESSION['admin']) || $_SESSION['admin']['position'] != 2) {
				echo json_encode(array('status' => 0));
				exit;
			}
			break;
		default:
			echo 'function checkLogin error';
			exit;
			break;
	}
			
}
function get_byte($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
function getDays($starttime,$endtime) {
	return (int)(($endtime-$starttime)/86400)+1;
}
function getAddresById($ids,$type=1,$str=','){
	if (empty($ids['provinceid']) && empty($ids['cityid']) && empty($ids['districtid'])) {
		return "";
	}
	if (isset($ids['provinceid']) && !empty($ids['provinceid'])) {
		$field[] = 'provinceName';
		$table[] = 'provinces';
		$where[] = 'provinces.provinceId='.$ids['provinceid'];
	}
	if (isset($ids['cityid']) && !empty($ids['cityid'])) {
		$field[] = 'cityName';
		$table[] = 'city';
		$where[] = 'city.cityId='.$ids['cityid'];
	}
	if (isset($ids['districtid']) && !empty($ids['districtid'])) {
		$field[] = 'districtName';
		$table[] = 'districts';
		$where[] = 'districts.districtId='.$ids['districtid'];
	}

	$fields = join(',', $field);
	$tables = join(',', $table);
	$wheres = join(' and ', $where);

	$sql = 'select '.$fields.' from '.$tables.' where '.$wheres;

	$res = M()->query($sql);
	if (empty($res)) {
		return false;
	}
	switch ($type) {
		case 1:
			foreach ($res[0] as $key => $value) {
				$result[] = $value;
			}
		    return $result;
			break;
		case 2:
			$ret = '';
			foreach ($res[0] as $key => $value) {
				$ret .= $value.$str;
			}
			return rtrim($ret,$str);
			break;
		default:
			return false;
			break;
	}
}

function gpc_stripslashes($string) {
	if(!is_array($string)) return stripslashes($string);
	foreach($string as $key => $val) $string[$key] = gpc_stripslashes($val);
	return $string;
}

function gpc_addslashes($string){
	if(!is_array($string)) return addslashes($string);
	foreach($string as $key => $val) $string[$key] = gpc_addslashes($val);
	return $string;
}

function getAgeByBirthday($birthday,$now=null){ 
	if ($birthday == '0000-00-00') {
		return 0;
	}
	list($y1,$m1,$d1) = explode("-",$birthday); 
	if ($now !== null) {
		$now = is_int($now) ? date("Y-m-d",$now) : $now;
	}else{
		$now = date('Y-m-d');
	}
	list($y2,$m2,$d2) = explode("-",$now); 
	$age = $y2 - $y1; 
	if((int)($m2.$d2) < (int)($m1.$d1)) 
		$age -= 1; 
	return $age; 
}
function generateToken($length){
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"§$%&/()=[]{}';

    $useChars = array();
    for ($i = 0; $i < $length; $i++) {
        $useChars[] = $characters[mt_rand(0, strlen($characters) - 1)];
    }
    array_push($useChars, rand(0, 9), rand(0, 9), rand(0, 9));
    shuffle($useChars);
    $randomString = trim(implode('', $useChars));
    $randomString = substr($randomString, 0, 16);

    return base64_encode($randomString);
}
function generateAlphaNumToken($length){
    $characters = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

    srand((float)microtime() * 1000000);

    $token = '';

    do
    {
        shuffle($characters);
        $token .= $characters[mt_rand(0, (count($characters) - 1))];
    } while (strlen($token) < $length);

    return $token;
}
function checkDateFormat($date){
	$preg = '/^(?:(?!0000)[0-9]{4}-(?:(?:0?[1-9]|1[0-2])-(?:0?[1-9]|1[0-9]|2[0-8])|(?:0?[13-9]|1[0-2])-(?:29|30)|(?:0?[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-0?2-29)$/';
	return preg_match($preg,$date) ? true : false; 
}
function checkVerify($code,$id=''){
	$verify = new \Think\Verify();
	return $verify->check($code,$id);
}
function getRelationName($relationid) {
	$relations = array('1' => '父亲',
						'2' => '母亲',
						'3' => '叔叔',
						'4' => '阿姨',
						'5' => '舅舅',
						'6' => '姨夫',
						'7' => '爷爷',
						'8' => '奶奶',
						'9' => '外公',
						'10' => '外婆'
						);
	return isset($relations[$relationid]) ? $relations[$relationid] : '未知';
}
function getParentSex($relationid) {
	return in_array($relationid,array('2','4','8','10')) ? '女' : '男';
}
function createPassword($str) {
	return md5($str.'pwdSalt');
}
function checkPosition($positionid,$type=false,$url=false) {
	if (!isset($_SESSION['admin']['position'])) {
		return false;
	}
	$ps = $_SESSION['admin']['position'];

	$flag = false;
	if (is_int($positionid)) {
		if ($positionid == $ps) {
			return true;
		}
		$flag = true;
	}

	if (!$flag && is_string($positionid)) {
		$positionid = explode(',', $positionid);
	}

	if (!$flag && is_array($positionid)) {
		foreach ($positionid as $key => $value) {
			if ($ps == $value) {
				return true;
			}
		}
		$flag = true;	
	}

	if ($flag) {
		if ($type) {
			if (IS_AJAX) {
				echo json_encode(array('code' => 0));
			}else{
				if (!$url) {
					switch ($ps) {
						case '1':
							$url = '/Home/Admin/index';
							break;
						case '2':
							$url = '/Home/Leader/index';
							break;
						case '3':
							$url = '/Home/Teacher/index';
							break;
						
						default:
							$url = '/Home/Index/index';
							break;
					}
				}
				redirect($url);
			}	
		}
	}
	return false;
}

function push($data,$url='http://127.0.0.1/service/push/index.php'){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// 设置请求为post类型
	curl_setopt($ch, CURLOPT_POST, 1);
	// 添加post数据到请求中
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('data' => json_encode($data)));

	// 执行post请求，获得回复
	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
}