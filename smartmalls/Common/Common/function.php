
<?php
function before_api(){
//var_dump($_SESSION('worker_id'));die;
//	session('mall.mall_id',1);
//	session('inst.institution_id',1);
//	session('merchant.merchant_id',1);
//	session('member.member_id',1);
//	session('isnt.inst_id',1);
//	session('worker_id',1);
//	session('worker_name','zhangsan');
//	session('role_id',0);
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
}

function checkLogin(){
}


function checkAuth(){

}

function is_phone($number){
	if(is_numeric($number) && substr($number,0,1)==1 && strlen($number)==11){
		return true;
	}
}
function get_state($state_id = 0){
	$state = array('0' => '未审核', '1' => '审核通过','2' => '审核不通过','4' => '未审核');
	return isset($state[$state_id]) ? $state[$state_id] : '未知';
}
function get_sex($sex_id = 1){
	$sex = array('1' => '男', '2' => '女');
	return isset($sex[$sex_id]) ? $sex[$sex_id] : '男';
}
function get_audit($state_id = 1){
	$state = array('1' => '未安排', '2' => '已安排');
	return isset($state[$state_id]) ? $state[$state_id] : '未安排';
}
function get_typeid($typeid_id){
	$typeid = array('1' => '天', '2' => '月', '3' => '季度', '4' => '年');
	return isset($typeid[$typeid_id]) ? $typeid[$typeid_id] : '未知';
}
function get_marriage($marriage_id){
	$marriage = array('1' => '已婚', '2' => '未婚', '3' => '丧偶', '4' => '离异');
	return isset($marriage[$marriage_id]) ? $marriage[$marriage_id] : '未知';
}

function get_health($health_id){
	$health = array('1' => '健康', '2' => '遗传性疾病', '3' => '非遗传性疾病');
	return isset($health[$health_id]) ? $health[$health_id] : '未知';
}
//1高中，2中专，3大专，4本科，5硕士，6博士，7博士后
function get_education($education_id){
	$education = array('1' => '高中', '2' => '中专', '3' => '大专', '4' => '本科', '5' => '硕士', '6' => '博士', '7' => '博士后');
	return isset($education[$education_id]) ? $education[$education_id] : '未知';
}

function get_parent_relation($relation_id){
	$relation = array('1' => '父亲', '2' => '母亲', '3' => '爷爷', '4' => '奶奶', '5' => '姥姥', '6' => '姥爷', '7' => '其他');
	return isset($relation[$relation_id]) ? $relation[$relation_id] : '未知';
}

function get_number_by_id($id){
	$id_len = strlen($id.'');
    if ($id_len < 6) {
        return str_repeat('0', 6 - $id_len).$id;
    }else{
        return $id.'';
    }
}

function fastRet($code,$info = null,$data = null,$other = array()){
	$ret['code'] = $code;
	if ($info !== null) {
		$ret['info'] = $info;
	}
	if ($data !== null) {
		$ret['data'] = $data;
	}
	if (!empty($other)) {
		foreach ($other as $key => $value) {
			$ret[$other['key']] = $other['value'];
		}
	}
	echo json_encode($ret);
}

//将数据包装成json
function Ret($data,$quit_after_ret = true, $type = 'json'){
	switch ($type) {
		case 'json':
			echo json_encode($data);
			break;
		
		default:
			# code...
			break;
	}
	$quit_after_ret && exit;
}

function getAddressById($ids,$type=1,$str=','){
	if (empty($ids['province_id']) && empty($ids['city_id']) && empty($ids['district_id'])) {
		return "";
	}
	if (isset($ids['province_id']) && !empty($ids['province_id'])) {
		$field[] = 'provinces.name as province_name';
		$table[] = 'provinces';
		$where[] = 'provinces.id='.$ids['province_id'];
	}
	if (isset($ids['city_id']) && !empty($ids['city_id'])) {
		$field[] = 'city.name as city_name';
		$table[] = 'city';
		$where[] = 'city.id='.$ids['city_id'];
	}
	if (isset($ids['district_id']) && !empty($ids['district_id'])) {
		$field[] = 'districts.name as district_name';
		$table[] = 'districts';
		$where[] = 'districts.id='.$ids['district_id'];
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

function getAgeByBirthday($birthday,$now=null){ 
	if (empty($birthday) || $birthday == '0000-00-00') {
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

function get_submit_data($key,$auto_ret = true,$quit_after_ret = true){
	$ret = '';
	switch ($key) {
		case 'name':
			$data = I($key);
			if (!$data) {
				$ret = '名字不能为空！';
				break;
			}
			if (strlen($data) > 20) {
				$ret = '名字长度不能超过20个字符！';
			}
			break;
		case 'phone':
			$data = I($key,'','intval');
			if (empty($data)) {
				$ret = '手机号码不能为空!';
				break;
			}
			if (!checkPhoneFormat($data)) {
				$ret = '手机号码格式有误！';
			}
			break;
		case 'email':
			
			break;
		case 'birthday':
			$data = I($key,'');
			if (!checkDateFormat($data)) {
				$ret = '生日格式有误！';
			}
			break;
		case 'sex':
			$data = I($key,0,'intval');
			if (empty($data)) {
				$ret = '请选择性别！';
				break;
			}
			$data = $data === 1 ? 1 : 2;
			break;
		case 'pic':
			$data = I($key,C('DEFAULT_PIC'));
			break;
		case 'id_number':
			$data = I($key,'');
			if (empty($data)) {
				$ret = '身份证号码不能为空！';
				break;
			}
			if (!checkIdNumber($data)) {
				$ret = '身份证号码格式有误！';
			}
			break;
		case 'username':
			$data = I($key,'');
			if (empty($data)) {
				$ret = '用户名不能为空！';
				break;
			}
			if (!checkUserName($data)) {
				$ret = '';
			}
			$plength = strlen($data);
			if ($plength < 6) {
				$ret = '用户名长度不能小于6个字符！';
				break;
			}
			if ($plength > 20) {
				$ret = '用户名长度不能超过20个字符！';
			}
			break;
		case 'password':
			$data = I($key);
			if (empty($data)) {
				$ret = '密码不能为空！';
				break;
			}
			$plength = strlen($data);
			if ($plength < 8) {
				$ret = '密码长度不能小于8个字符！';
				break;
			}
			if ($plength > 20) {
				$ret = '密码长度不能大于20个字符！';
			}
			break;
		default:
			return I($key);
			break;
	}
	if ($ret === '') {
		return $data;
	}elseif($auto_ret){
		Ret(array('code' => 2, 'info' => $ret),$quit_after_ret);
	}else{
		return false;
	}
}

function get_basic_data($keys = null){
	if ($keys === null) {
		$keys = array('name','phone','sex','birthday','pic','id_number');
	}else{
		return false;
	}

	foreach ($keys as $key => $value) {
		$ret[$value] = get_submit_data($value);
		if ($ret[$value] === false) {
			echo '获取基本信息出错！';
			die;
		}
	}
	return $ret;
}

function createPassword($str) {
	return md5(md5($str.'cenktech'));
}

function checkDateFormat($date){
	$preg = '/^(?:(?!0000)[0-9]{4}-(?:(?:0?[1-9]|1[0-2])-(?:0?[1-9]|1[0-9]|2[0-8])|(?:0?[13-9]|1[0-2])-(?:29|30)|(?:0?[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-0?2-29)$/';
	return preg_match($preg,$date) ? true : false; 
}

function checkPhoneFormat($phone){
	$preg = "/^1[34578]\d{9}$/";
	return preg_match($preg,$phone) ? true : false; 
}

function checkEmail($email){
	$preg = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
	return preg_match($preg,$email) ? true : false; 
}

function checkUserName($username){
	$preg = '^w+$';
	return preg_match($preg,$username) ? true : false; 
}


function checkIdNumber($id_card){
    if(strlen($id_card)==18){
        return idcard_checksum18($id_card);
    }elseif((strlen($id_card)==15)){
        $id_card=idcard_15to18($id_card);
        return idcard_checksum18($id_card);
    }else{
        return false;
    }
}


/**
 * 验证手机号是否正确
 * @author honfei
 * @param number $mobile
 */

function isMobile($mobile) {
	if (!is_numeric($mobile)) {
		return false;
	}
	return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}



/**
 * 判断是否为合法的身份证号码
 * @param $mobile
 * @return int
 */
function isCreditNo($vStr){
	$vCity = array(
			'11','12','13','14','15','21','22',
			'23','31','32','33','34','35','36',
			'37','41','42','43','44','45','46',
			'50','51','52','53','54','61','62',
			'63','64','65','71','81','82','91'
	);
	if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;
	if (!in_array(substr($vStr, 0, 2), $vCity)) return false;
	$vStr = preg_replace('/[xX]$/i', 'a', $vStr);
	$vLength = strlen($vStr);
	if ($vLength == 18) {
		$vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
	} else {
		$vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
	}
	if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;

	return true;
}



// 计算身份证校验码，根据国家标准GB 11643-1999
function idcard_verify_number($idcard_base){
    if(strlen($idcard_base)!=17){
        return false;
    }
    //加权因子
    $factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
    //校验码对应值
    $verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2');
    $checksum=0;
    for($i=0;$i<strlen($idcard_base);$i++){
        $checksum += substr($idcard_base,$i,1) * $factor[$i];
    }
    $mod=$checksum % 11;
    $verify_number=$verify_number_list[$mod];
    return $verify_number;
}
// 将15位身份证升级到18位
function idcard_15to18($idcard){
    if(strlen($idcard)!=15){
        return false;
    }else{
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if(array_search(substr($idcard,12,3),array('996','997','998','999')) !== false){
            $idcard=substr($idcard,0,6).'18'.substr($idcard,6,9);
        }else{
            $idcard=substr($idcard,0,6).'19'.substr($idcard,6,9);
        }
    }
    $idcard=$idcard.idcard_verify_number($idcard);
    return $idcard;
}
// 18位身份证校验码有效性检查
function idcard_checksum18($idcard){
    if(strlen($idcard)!=18){
        return false;
    }
    $idcard_base=substr($idcard,0,17);
    if(idcard_verify_number($idcard_base)!=strtoupper(substr($idcard,17,1))){
        return false;
    }else{
        return true;
    }
}

//获取筹办单位
function get_host(){
	$merchant=D('Merchant')->get_merchant();
	$inst=D('Inst')->get_institution();
	$mall=D('Mall')->get_mall();
	Ret(array('inst'=>$inst,'mall'=>$mall,'merchant'=>$merchant));

}

//计算时间（结果分钟）
function get_time($start_time,$end_time){

	$start_time=strtotime ($start_time); //当前时间  ,注意H 是24小时 h是12小时
	$end_time=strtotime ($end_time);  //过年时间，不能写2014-1-21 24:00:00  这样不对
	$time=ceil(($end_time-$start_time)/60); //60s*60min*24h
	return $time;

}
/*
 * 加密
 */
function encrypt($data, $key) {
	$prep_code = serialize($data);
	$block = mcrypt_get_block_size('des', 'ecb');
	if (($pad = $block - (strlen($prep_code) % $block)) < $block) {
		$prep_code .= str_repeat(chr($pad), $pad);
	}
	$encrypt = mcrypt_encrypt(MCRYPT_DES, $key, $prep_code, MCRYPT_MODE_ECB);
	return base64_encode($encrypt);
}

function decrypt($str, $key) {
	$str = base64_decode($str);
	$str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
	$block = mcrypt_get_block_size('des', 'ecb');
	$pad = ord($str[($len = strlen($str)) - 1]);
	if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str)) {
		$str = substr($str, 0, strlen($str) - $pad);
	}
	return unserialize($str);
}
function writeLog($content)
{
	$file  = 'log1.txt';
	if($f  = file_put_contents($file, $content,FILE_APPEND)) {
		echo date('Y-m-d H:i:s') + "写入成功。<br />";
	}
}
function exportExcel($expTitle,$expCellName,$expTableData){
	$xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
	//$fileName = $_SESSION['account'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
	$fileName = date('YmdHis');
	$cellNum = count($expCellName);
	$dataNum = count($expTableData);
	vendor("PHPExcel.Classes.PHPExcel");

	$objPHPExcel = new \PHPExcel();
	$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

	$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
	// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
	for($i=0;$i<$cellNum;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
	}
	// Miscellaneous glyphs, UTF-8
	for($i=0;$i<$dataNum;$i++){
		for($j=0;$j<$cellNum;$j++){
			$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
		}
	}

	header('pragma:public');
	header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
	header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
}

/**
 * UTF-8编码 GBK编码相互转换/（支持数组）
 *
 * @param array $str   字符串，支持数组传递
 * @param string $in_charset 原字符串编码
 * @param string $out_charset 输出的字符串编码
 * @return array
 */
function array_iconv($str, $in_charset="GBK", $out_charset="UTF-8")
{
	if(is_array($str))
	{
		foreach($str as $k => $v)
		{
			$str[$k] = array_iconv($v);
		}
		return $str;
	}
	else
	{
		if(is_string($str))
		{
			// return iconv('UTF-8', 'GBK//IGNORE', $str);
			return mb_convert_encoding($str, $out_charset, $in_charset);
		}
		else
		{
			return $str;
		}
	}
}


/*
 * 获取机构收费项目
 */
function getInstitutionPayment(){
    return array(array('id'=>1,'name'=>'物业费'),array('id'=>2,'name'=>'宽带费'),array('id'=>3,'name'=>'房租费'),array('id'=>4,'name'=>'管理费'));
}




function getMonthNum( $start_time, $end_time, $tags='-' )
{
    return (strtotime($end_time) - strtotime($start_time)) / (86400 * 30);
}
function getDayNum($start_time, $end_time, $tags='-')
{
    return (strtotime($end_time) - strtotime($start_time)) / 86400;
}

/**
 * 数组转xls格式的excel文件
 * @param  array  $data      需要生成excel文件的数组
 * @param  string $filename  生成的excel文件名
 *      示例数据：
$data = array(
array(NULL, 2010, 2011, 2012),
array('Q1',   12,   15,   21),
array('Q2',   56,   73,   86),
array('Q3',   52,   61,   69),
array('Q4',   30,   32,    0),
);
 */
function createXLS($data,$filename='simple.xls'){
	ini_set('max_execution_time', '0');
	Vendor('PHPExcel.PHPExcel');
	$filename=str_replace('.xls', '', $filename).'.xls';
	$phpexcel = new PHPExcel();
	$phpexcel->getProperties()
		->setCreator("Maarten Balliauw")
		->setLastModifiedBy("Maarten Balliauw")
		->setTitle("Office 2007 XLSX Test Document")
		->setSubject("Office 2007 XLSX Test Document")
		->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		->setKeywords("office 2007 openxml php")
		->setCategory("Test result file");
	$phpexcel->getActiveSheet()->fromArray($data);
	$phpexcel->getActiveSheet()->setTitle('Sheet1');
	$phpexcel->setActiveSheetIndex(0);
	header('Content-Type: application/vnd.ms

-excel');
	header("Content-Disposition: attachment;filename=$filename");
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1');
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	$objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
	$objwriter->save('php://output');
	exit;
}
