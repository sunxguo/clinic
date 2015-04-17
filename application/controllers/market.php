<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
@session_start();
class Market extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->helper("base");
		$this->load->helper("phpqrcode");
		$this->load->model("dbHandler");
		$this->language();
	}
	public function language(){
		$this->load->helper('language');
		if(isset($_SESSION['language'])){
			if($_SESSION['language']=="english"){
				$this->config->set_item('language', 'english');
				$this->load->language('market','english');
				return true;
			}elseif($_SESSION['language']=="tw_cn"){
				$this->config->set_item('language', 'tw_cn');
				$this->load->language('market','tw_cn');
				return true;
			}else{
				$this->config->set_item('language', 'zh_cn');
				$this->load->language('market','zh_cn');
				return true;
			}
		}
		//判断浏览器语言
		$default_lang_arr = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$strarr = explode(",",$default_lang_arr);
		$default_lang = $strarr[0];
//		echo '1'.$default_lang;
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4); //只取前4位，这样只判断最优先的语言。如果取前5位，可能出现en,zh的情况，影响判断。  
		if (preg_match("/en/i", $lang)){ 
			$this->config->set_item('language', 'english');
			// 根据设置的语言类型加载语言包
			$this->load->language('market','english');
		}
		elseif (preg_match("/zh-c/i", $lang)){
			$this->config->set_item('language', 'zh_cn');
			$this->load->language('market','zh_cn');
		}
		elseif (preg_match("/zh/i", $lang)){ 
			$this->config->set_item('language', 'tw_cn');
			$this->load->language('market','tw_cn');
		}else{
			$this->config->set_item('language', 'zh_cn');
			$this->load->language('market','zh_cn');
		}
/*		// 根据浏览器类型设置语言
		if( $default_lang == 'en-us' || $default_lang == 'en'){
			$this->config->set_item('language', 'english');
			// 根据设置的语言类型加载语言包
			$this->load->language('market','english');
		}elseif( $default_lang == 'en-us' || $default_lang=='zh-CN'){
			$this->config->set_item('language', 'zh_cn');
			$this->load->language('market','zh_cn');
		}
		// 当前语言
		echo $this->config->item('language');*/
	}
	public function set_language(){
		$_SESSION['language']=$_POST['language'];
	}
	public function index(){
		$data["marketscroll"]=$this->dbHandler->selectalldatadesc("marketscroll","order_marketscroll","ASC");
		$marketad=$this->dbHandler->selectPartData('marketad','id_marketad',1);
		$data["marketad"]=$marketad[0];
		//装机必备
		$necessary=$this->dbHandler->selectPartData('marketunion','special_marketunion',1);
		$data["necessary"]=$necessary[0];
		if($data["necessary"]->appidarray_marketunion!="") $apps=json_decode($data["necessary"]->appidarray_marketunion);
		else $apps=array();
		foreach($apps as $a){
			$app=$this->dbHandler->selectPartData('app','id_app',$a->appid);
			$app=$app[0];
			$app->star=$this->getAverageStar($app->totalstar_app,$app->commentnum_app);
			$data["necessaryapps"][]=$app;
		}
		//精品软件
		$select=$this->dbHandler->selectPartData('marketunion','special_marketunion',2);
		$data["select"]=$select[0];
		if($data["select"]->appidarray_marketunion!="") $apps=json_decode($data["select"]->appidarray_marketunion);
		else $apps=array();
		foreach($apps as $a){
			$app=$this->dbHandler->selectPartData('app','id_app',$a->appid);
			$app=$app[0];
			$app->star=$this->getAverageStar($app->totalstar_app,$app->commentnum_app);
			$data["selectapps"][]=$app;
		}
		//生活
		$life=$this->dbHandler->selectPartData('marketunion','special_marketunion',3);
		$data["life"]=$life[0];
		if($data["life"]->appidarray_marketunion!="") $apps=json_decode($data["life"]->appidarray_marketunion);
		else $apps=array();
		foreach($apps as $a){
			$app=$this->dbHandler->selectPartData('app','id_app',$a->appid);
			$app=$app[0];
			$app->star=$this->getAverageStar($app->totalstar_app,$app->commentnum_app);
			$data["lifeapps"][]=$app;
		}
		//新闻
		$news=$this->dbHandler->selectPartData('marketunion','special_marketunion',4);
		$data["news"]=$news[0];
		if($data["news"]->appidarray_marketunion!="") $apps=json_decode($data["news"]->appidarray_marketunion);
		else $apps=array();
		foreach($apps as $a){
			$app=$this->dbHandler->selectPartData('app','id_app',$a->appid);
			$app=$app[0];
			$app->star=$this->getAverageStar($app->totalstar_app,$app->commentnum_app);
			$data["newsapps"][]=$app;
		}
		//娱乐
		$entertainment=$this->dbHandler->selectPartData('marketunion','special_marketunion',5);
		$data["entertainment"]=$entertainment[0];
		if($data["entertainment"]->appidarray_marketunion!="") $apps=json_decode($data["entertainment"]->appidarray_marketunion);
		else $apps=array();
		foreach($apps as $a){
			$app=$this->dbHandler->selectPartData('app','id_app',$a->appid);
			$app=$app[0];
			$app->star=$this->getAverageStar($app->totalstar_app,$app->commentnum_app);
			$data["entertainmentapps"][]=$app;
		}
		//购物
		$shopping=$this->dbHandler->selectPartData('marketunion','special_marketunion',6);
		$data["shopping"]=$shopping[0];
		if($data["shopping"]->appidarray_marketunion!="") $apps=json_decode($data["shopping"]->appidarray_marketunion);
		else $apps=array();
		foreach($apps as $a){
			$app=$this->dbHandler->selectPartData('app','id_app',$a->appid);
			$app=$app[0];
			$app->star=$this->getAverageStar($app->totalstar_app,$app->commentnum_app);
			$data["shoppingapps"][]=$app;
		}
		//精选专题
		$selectUnions=$this->dbHandler->SDUNR('marketunion',array("special_marketunion"=>0,"home_marketunion"=>1),array("col"=>'id_marketunion',"by"=>'desc'));
		$data["selectUnions"]=$selectUnions;
		foreach($selectUnions as $key=>$union){
			if($key<2){
				if($union->appidarray_marketunion!="null" && $union->appidarray_marketunion!="")
					$apps=json_decode($union->appidarray_marketunion);
				else $apps=array();
				foreach($apps as $a){
					$app=$this->dbHandler->selectPartData('app','id_app',$a->appid);
					$data["selectUnionsApps"][$key][]=$app[0];
				}
			}
		}
		//综合排名
		$data["comprehensiveList"]=$this->dbHandler->selectdatacustomorder("app","state_app",5,7,0,"commentnum_app desc,totalstar_app desc,download_time_app desc");
		//下载排行
		$data["downloadList"]=$this->dbHandler->selectdata("app","state_app",5,7,0,"download_time_app","desc");
		//上升排行
		$data["riseList"]=$this->dbHandler->selectdata("app","state_app",5,7,0,"download_lasttime_app","desc");
		$this->load->view('header',
			array(
				'title' => WEBSITE_NAME."-首页"
			)
		);
		$data["categories"]=$this->get_category();
		$this->load->view('index', $data);
		$this->load->view('footer');
	}
	public function appcategory(){
		$page=isset($_GET["page"]) && is_numeric($_GET['page']) && $_GET['page']>0?$_GET["page"]:1;
		$orlike=$like=array();
		$amount=0;//总条数
		$limit=32;//每页显示
		if(isset($_GET['search']) && $_GET['search']!=""){
			$like["name_app"]=$_GET['search'];
			$orlike["desc_app"]=$_GET['search'];
		}
		else $like=array();
		$condition=array("state_app"=>5);
		if(isset($_GET['cid']) && is_numeric($_GET['cid'])) $condition["cat_app"]=$_GET['cid'];
		$amount=$this->dbHandler->ADUlike('app',$condition,$like,$orlike);
		$page_amount=ceil($amount/$limit);//总页数
		if($page>$page_amount) $page=1;
		$offset=($page-1)*$limit;
		$order="create_time_app desc";
		if(isset($_GET['sort']) && $_GET['sort']=="correlation ") $order="create_time_app desc";
		if(isset($_GET['sort']) && $_GET['sort']=="hot") $order="download_time_app desc";
		if(isset($_GET['sort']) && $_GET['sort']=="new") $order="update_time_app desc";
		if(isset($_GET['sort']) && $_GET['sort']=="comprehension") $order="commentnum_app desc,totalstar_app desc,download_time_app desc";
		$apps=$this->dbHandler->SDSDlikeCusOrder('app',$condition,array("limit"=>$limit,"offset"=>$offset),$order,$like,$orlike);
		$ex_url="";
		if(isset($_GET["cid"]))$ex_url.="&cid=".$_GET["cid"];
		if(isset($_GET["search"]))$ex_url.="&search=".$_GET["search"];
		if(isset($_GET["sort"]))$ex_url.="&sort=".$_GET["sort"];
		$prev_link=$page>1?"/market/appcategory?page=".($page-1).$ex_url:"no";
		$next_link=$page<$page_amount?"/market/appcategory?page=".($page+1).$ex_url:"no";
		$first_link=($page!=1)?"/market/appcategory?page=1".$ex_url:"no";
		$last_link=($page!=$page_amount)?"/market/appcategory?page=".$page_amount.$ex_url:"no";
		$jump_link="/market/appcategory?jump=1".$ex_url."&page=";
		$cid=isset($_GET["cid"])?"&cid=".$_GET["cid"]:"";
		$search=isset($_GET["search"])?"&search=".$_GET["search"]:"";
		$select_link="/market/appcategory?page=1".$cid.$search;
		$this->load->view('header',
			array(
				'title' => WEBSITE_NAME."-应用下载",
			)
		);
		$start=$page>3?$page-2:1;
		$end=($page+2)<=$page_amount?$page+2:$page_amount;
		
		$data=array(
				"apps"=>$apps,
				"categories"=>$this->get_category(),
				"prev_link"=>$prev_link,
				"next_link"=>$next_link,
				"first_link"=>$first_link,
				"jump_link"=>$jump_link,
				"last_link"=>$last_link,
				"select_link"=>$select_link,
				"page"=>$page,
				"page_amount"=>$page_amount,
				"amount"=>$amount,
				"start"=>$start,
				"end"=>$end
			);
		$this->load->view('appcategory', $data);
		$this->load->view('footer');
	}
	public function appunion(){
		$page=isset($_GET["page"]) && is_numeric($_GET['page']) && $_GET['page']>0?$_GET["page"]:1;
		$orlike=$like=array();
		$amount=0;//总条数
		$limit=5;//每页显示
		if(isset($_GET['search']) && $_GET['search']!=""){
			$like["name_marketunion"]=$_GET['search'];
			$orlike["description_merchant"]=$_GET['search'];
		}
		else $like=array();
		$condition=array("special_marketunion"=>0);
		$amount=$this->dbHandler->ADUlike('marketunion',$condition,$like,$orlike);
		$page_amount=ceil($amount/$limit);//总页数
		if($page>$page_amount) $page=1;
		$offset=($page-1)*$limit;
		$marketunion=$this->dbHandler->SDSDlike('marketunion',$condition,array("limit"=>$limit,"offset"=>$offset),array("col"=>'id_marketunion',"by"=>'desc'),$like,$orlike);
		foreach($marketunion as $u){
			if(isset($u->appidarray_marketunion) && is_array(json_decode($u->appidarray_marketunion))){
				foreach(json_decode($u->appidarray_marketunion) as $key=>$a){
					$u->app[$key]=$this->get_info_return("app",$a->appid);
					$u->app[$key]->star=$this->getAverageStar($u->app[$key]->totalstar_app,$u->app[$key]->commentnum_app);
				}
			}else $u->app=array();
			if(!isset($u->app)) $u->app=array();
		}
		$ex_url="";
		if(isset($_GET["search"]))$ex_url.="&search=".$_GET["search"];
		$prev_link=$page>1?"/market/appunion?page=".($page-1).$ex_url:"no";
		$next_link=$page<$page_amount?"/market/appunion?page=".($page+1).$ex_url:"no";
		$first_link=($page!=1)?"/market/appunion?page=1".$ex_url:"no";
		$last_link=($page!=$page_amount)?"/market/appunion?page=".$page_amount.$ex_url:"no";
		$jump_link="/market/appunion?jump=1".$ex_url."&page=";
		$select_link="/market/appunion?jump=1";
		$start=$page>3?$page-2:1;
		$end=($page+2)<=$page_amount?$page+2:$page_amount;
		$this->load->view('header',
			array(
				'title' => WEBSITE_NAME."-应用专题",
			)
		);
		$this->load->view('appunion',
			array(
				"marketunion"=>$marketunion,
				"category"=>$this->get_category(),
				"prev_link"=>$prev_link,
				"next_link"=>$next_link,
				"first_link"=>$first_link,
				"jump_link"=>$jump_link,
				"last_link"=>$last_link,
				"select_link"=>$select_link,
				"page"=>$page,
				"page_amount"=>$page_amount,
				"amount"=>$amount,
				"start"=>$start,
				"end"=>$end
			));
		$this->load->view('footer');
	}
	public function app(){
		if(!isset($_GET['appid']) || !is_numeric($_GET['appid'])){
			$this->load->view('redirect',array("url"=>"/","info"=>"app的id不正确"));
			return false;
		}
		$app=$this->dbHandler->selectPartData('app','id_app',$_GET['appid']);
		$app=$app[0];
		$app->star=$this->getAverageStar($app->totalstar_app,$app->commentnum_app);
		$merchant=$this->dbHandler->selectPartData('merchant','id_merchant',$app->merchant_id_app);
		$merchant=$merchant[0];
		$previewImgs=$this->dbHandler->SDUNR('previewimg',array("appid_previewimg"=>$_GET['appid']),array("col"=>'ordernum_previewimg',"by"=>'asc'));
		
		//推荐
		$recommend=$this->dbHandler->selectPartData('marketunion','special_marketunion',6);
		if($recommend[0]->appidarray_marketunion!="") $apps=json_decode($recommend[0]->appidarray_marketunion);
		else $apps=array();
		$recommendapps=array();
		foreach($apps as $a){
			$recommendapp=$this->dbHandler->selectPartData('app','id_app',$a->appid);
			$recommendapps[]=$recommendapp[0];
		}
		$limit=10;
		$page=isset($_GET["page"]) && is_numeric($_GET['page']) && $_GET['page']>0?$_GET["page"]:1;
		$condition=array("appid_comment"=>$_GET['appid']);
		$amount=$this->dbHandler->ADUlike('comment',$condition,array(),array());
		$page_amount=ceil($amount/$limit);//总页数
		if($page>$page_amount) $page=1;
		$offset=($page-1)*$limit;
//		$comments=$this->dbHandler->SDUNR('comment',array("appid_comment"=>$_GET['appid']),array("col"=>'time_comment',"by"=>'desc'));
		$comments=$this->dbHandler->SDSDlike('comment',$condition,array("limit"=>$limit,"offset"=>$offset),array("col"=>'time_comment',"by"=>'desc'),array(),array());
		$prev_link=$page>1?"/market/app?page=".($page-1):"no";
		$next_link=$page<$page_amount?"/market/app?page=".($page+1):"no";
		$first_link=($page!=1)?"/market/app?page=1":"no";
		$last_link=($page!=$page_amount)?"/market/app?page=".$page_amount:"no";
		$jump_link="/market/app?jump=1&page=";
		$select_link="/market/app?jump=1";
		$start=$page>3?$page-2:1;
		$end=($page+2)<=$page_amount?$page+2:$page_amount;
		$this->load->view('header',
			array(
				'title' =>$app->name_app."-应用下载-".WEBSITE_NAME,
			)
		);
		$this->load->view('app',
			array(
				"app"=>$app,
				"categories"=>$this->get_category(),
				"merchant"=>$merchant,
				"previewImgs"=>$previewImgs,
				"comments"=>$comments,
				"recommendapps"=>$recommendapps,
				"prev_link"=>$prev_link,
				"next_link"=>$next_link,
				"first_link"=>$first_link,
				"jump_link"=>$jump_link,
				"last_link"=>$last_link,
				"select_link"=>$select_link,
				"page"=>$page,
				"page_amount"=>$page_amount,
				"amount"=>$amount,
				"start"=>$start,
				"end"=>$end
			)
		);
		$this->load->view('footer');
	}
	public function preview(){
		if(!isset($_GET['appid']) || !is_numeric($_GET['appid'])){
			$this->load->view('redirect',array("url"=>"/","info"=>"app的id不正确"));
			return false;
		}
		$app=$this->dbHandler->selectPartData('app','id_app',$_GET['appid']);
		$app=$app[0];
		if(file_exists("/uploads/2dcode/wap".$app->id_app.'withlogo.png')){
			$app->mobilewebsite2dcode="/uploads/2dcode/wap".$app->id_app.'withlogo.png';
		}else{
			$text=WEBSITE_URL."/mobile/home?appid=".$app->id_app;
			$appid="wap".$app->id_app;
			$logoSrc=$app->icon_app;
			$app->mobilewebsite2dcode=$this->twoDimensionCode($text,$appid,$logoSrc);
		}
		//推荐
		$recommend=$this->dbHandler->selectPartData('marketunion','special_marketunion',6);
		if($recommend[0]->appidarray_marketunion!="") $apps=json_decode($recommend[0]->appidarray_marketunion);
		else $apps=array();
		$recommendapps=array();
		foreach($apps as $a){
			$recommendapp=$this->dbHandler->selectPartData('app','id_app',$a->appid);
			$recommendapps[]=$recommendapp[0];
		}
		$this->load->view('header',
			array(
				'title' =>$app->name_app."-手机网站预览-".WEBSITE_NAME,
			)
		);
		$this->load->view('preview',
			array(
				"app"=>$app,
				"recommendapps"=>$recommendapps
			)
		);
		$this->load->view('footer');
	}
	public function download(){
		if(!isset($_GET['appid']) || !is_numeric($_GET['appid'])){
			$this->load->view('redirect',array("url"=>"/","info"=>"app的id不正确"));
			return false;
		}
		$app=$this->dbHandler->selectPartData('app','id_app',$_GET['appid']);
		$app=$app[0];
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$iphone = (strpos($agent, 'iphone')) ? true : false;
		$ipad = (strpos($agent, 'ipad')) ? true : false;
		$android = (strpos($agent, 'android')) ? true : false;
		if($iphone || $ipad){
			if($app->ios_link_app=="null" ||$app->ios_link_app==null) echo "IOS应用暂未生成，请稍后！";
			else echo "<script>window.location.href='".$app->ios_link_app."#mp.weixin.qq.com'</script>";
		}elseif($android){
			echo "<script>window.location.href='".$app->android_link_app."#mp.weixin.qq.com'</script>";  
		}else{
			echo "未知设备...正在跳转<script>window.location.href='/market/app?appid=".$app->id_app."#mp.weixin.qq.com'</script>";
		}
	}
	public function getAverageStar($totalStar,$commentNnum){
		if($commentNnum==0) return "5";
		$average=$totalStar/$commentNnum;
		if($average==0) return "0";
		if($average>0  && $average<0.5) return "05";
		if($average>=0.5 && $average<1) return "1";
		if($average>=1 && $average<1.5) return "15";
		if($average>=1.5 && $average<2) return "2";
		if($average>=2 && $average<2.5) return "25";
		if($average>=2.5 && $average<3) return "3";
		if($average>=3 && $average<3.5) return "35";
		if($average>=3.5 && $average<4) return "4";
		if($average>=4 && $average<4.5) return "45";
		if($average>=4.5 && $average<=5) return "5";
	}
	public function get_categories(){
		 return $this->dbHandler->selectalldatadesc("category","id_category","ASC");
	}
	public function get_category(){
		$category=$this->dbHandler->selectAllData("category");
		$new_cat=array();
		foreach($category as $cat){
			$new_cat[$cat->id_category]=$cat->name_category;
		}
		return $new_cat;
	}
	public function get_info_return($info_type,$id){
		switch($info_type){
			case 'app':
				$table="app";
				$where="id_app";
				$content=$id;
			break;
			case 'message':
				$table="message";
				$where="id_message";
				$content=$id;
			break;
			case 'merchant':
				$table="merchant";
				$where="id_merchant";
				$content=$id;
			break;
			case 'marketunion':
				$table="marketunion";
				$where="special_marketunion";
				$content=$id;
			break;
		}
		$result=$this->dbHandler->selectPartData($table,$where,$content);
		if(sizeof($result) >0) return $result[0];
		else return new stdClass();
	}
	
	public function add_info(){
		switch($_POST['info_type']){
			case "comment":
				$table="comment";
				$info=array(
					"appid_comment"=>$_POST['appid'],
					"star_comment"=>$_POST['star'],
					"user_comment"=>$_POST['user'],
//					"title_comment"=>$_POST['title'],
					"text_comment"=>$_POST['text'],
					"time_comment"=>date("Y-m-d H:i:s")
				);
				$app=$this->get_info_return("app",$_POST['appid']);
				$modifyInfo=array(
					"totalstar_app"=>($app->totalstar_app+$_POST['star']),
					"commentnum_app"=>($app->commentnum_app+1)
				);
				$this->dbHandler->updatedata("app",$modifyInfo,"id_app",$_POST['appid']);
			break;
		}
		$result=$this->dbHandler->insertdata($table,$info);
		if($result==1) echo json_encode(array("result"=>"success","message"=>"信息写入成功"));
		else echo json_encode(array("result"=>"failed","message"=>"信息写入失败"));
	}
	public function twoDimensionCode($text,$appid,$logoSrc){
		$value = $text; //二维码内容   
		$errorCorrectionLevel = 'H';//容错级别   
		$matrixPointSize = 10;//生成图片大小    
		$QR = $_SERVER['DOCUMENT_ROOT'].'/uploads/2dcode/'.$appid.'qrcode.png';//已经生成的原始二维码图    
		
		//生成二维码图片
		QRcode::png($value,$QR, $errorCorrectionLevel, $matrixPointSize, 2);
		$logo = $_SERVER['DOCUMENT_ROOT'].$logoSrc;//准备好的logo图片 

		if ($logo !== FALSE) {
			$QR = imagecreatefromstring(file_get_contents($QR));   
			$logo = imagecreatefromstring(file_get_contents($logo));   
			$QR_width = imagesx($QR);//二维码图片宽度   
			$QR_height = imagesy($QR);//二维码图片高度   
			$logo_width = imagesx($logo);//logo图片宽度   
			$logo_height = imagesy($logo);//logo图片高度   
			$logo_qr_width = $QR_width / 4;
			$scale = $logo_width/$logo_qr_width;
			$logo_qr_height = $logo_height/$scale;
			$from_width = ($QR_width - $logo_qr_width) / 2;
			//重新组合图片并调整大小   
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,   
			$logo_qr_height, $logo_width, $logo_height);   
		}   
		//输出图片地址
		$dstLocation='/uploads/2dcode/'.$appid.'withlogo.png';
		//输出图片   
		imagepng($QR,$_SERVER['DOCUMENT_ROOT'].$dstLocation);   
		return  $dstLocation; 
	}
}
?>