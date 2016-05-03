<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
@session_start();

class Api extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->helper("base");
		$this->load->helper("upload");
		$this->load->model("dbHandler");
	}
	public function index()
	{
		if(!isset($_GET['appid']) || !is_numeric($_GET['appid'])){
			$this->load->view('redirect',array("info"=>"app的id不正确"));
			return false;
		}
		$app=$this->dbHandler->selectPartData('app','id_app',$_GET['appid']);
		$ymxz=$this->dbHandler->selectPartData('nav','id_nav','111');
		$zdqb=$this->dbHandler->selectPartData('nav','id_nav','112');
		$link=$this->dbHandler->selectPartData('link','navid_link',$zdqb[0]->id_nav);
		$zdqb[0]->link=$link[0]->url_link;
		$navs=$this->dbHandler->SDUNR('nav',array("app_id_nav"=>$_GET['appid']),array("col"=>'order_nav',"by"=>'asc'));
		foreach($navs as $n){
			if($n->type_nav==6){
				$link=$this->dbHandler->selectPartData('link','navid_link',$n->id_nav);
				$n->link=$link[0]->url_link;
			}
		}
		$sliders=$this->dbHandler->SDUNR('homeslider',array("appid_homeslider"=>$_GET['appid']),array("col"=>'ordernum_homeslider',"by"=>'asc'));
		$this->load->view('mobile/home',
			array(
				'showSlider' => true,
				'title' => ($app[0]->name_app)."-手機網站",
				'app'=>$app[0],
				'navs'=>$navs,
				'ymxz'=>$ymxz[0],
				'zdqb'=>$zdqb[0],
				'sliders'=>$sliders
			)
		);
	}
	public function nav(){
		$echoData=new stdClass;
		if(!isset($_GET['appid']) || !is_numeric($_GET['appid'])){
			//1->Appid Error!
			$echoData->result=1;
			$echoData->data='app id 错误！';
			echo json_encode($echoData);
			return false;
		}
		$ymxz=$this->dbHandler->selectPartData('nav','id_nav','111');
		$zdqb=$this->dbHandler->selectPartData('nav','id_nav','112');
		$navs=$this->dbHandler->SDUNR('nav',array("app_id_nav"=>$_GET['appid']),array("col"=>'order_nav',"by"=>'asc'));
		$navs[sizeof($navs)]=$ymxz[0];
		$navs[sizeof($navs)+1]=$zdqb[0];
		$data=array();
		foreach($navs as $n){
			$nav=new stdClass;
			$nav->id=$n->id_nav;
			$nav->name=$n->name_nav;
			$nav->icon='http://clinic.coolkeji.com'.$n->icon_nav;
			$nav->type=$n->type_nav;
			if($n->type_nav==6){
				$link=$this->dbHandler->selectPartData('link','navid_link',$n->id_nav);
				$nav->link=$link[0]->url_link;
			}
			$data[]=$nav;
		}
//		print_r($data);
		$echoData->result=0;
		$echoData->data=$data;
		echo json_encode($echoData);
//		echo json_encode(array("result"=>0,"data"=>$data));
	}
	public function essaylist(){
		$echoData=new stdClass;
		if(!isset($_GET['navid']) || !is_numeric($_GET['navid'])){
			//1->Appid Error!
			$echoData->result=1;
			$echoData->data='nav id 错误！';
			echo json_encode($echoData);
			return false;
		}
		$essays=$this->dbHandler->SDUNR('essay',array("navid_essay"=>$_GET['navid']),array("col"=>'lasttime_essay',"by"=>'desc'));
		$data=array();
		foreach($essays as $e){
			$essay=new stdClass;
			$essay->id=$e->id_essay;
			$essay->title=$e->title_essay;
			$essay->summary=$e->summary_essay;
//			$essay->text=$e->text_essay;
			$thumbnails=json_decode($e->thumbnail_essay);
			$essay->thumbnail=isset($thumbnails[0])?'http://clinic.coolkeji.com'.$thumbnails[0]->src:'';
			$data[]=$essay;
		}
		$echoData->result=0;
		$echoData->data=$data;
		echo json_encode($echoData);
	}
	public function essay(){
		$echoData=new stdClass;
		if(!isset($_GET['essayid']) || !is_numeric($_GET['essayid'])){
			//1->Appid Error!
			$echoData->result=1;
			$echoData->data='essay id 错误！';
			echo json_encode($echoData);
			return false;
		}
		$essay=$this->dbHandler->selectPartData('essay','id_essay',$_GET['essayid']);
		$essay=$essay[0];
		$data=new stdClass;
		$data->id=$essay->id_essay;
		$data->navid=$essay->navid_essay;
		$data->title=$essay->title_essay;
		$data->summary=$essay->summary_essay;
		$data->text=$essay->text_essay;
		$data->thumbnail=$essay->thumbnail_essay;
		$echoData->result=0;
		$echoData->data=$data;
		echo json_encode($echoData);
	}
	public function content(){
		$echoData=new stdClass;
		if(!isset($_GET['navid']) || !is_numeric($_GET['navid'])){
			//1->Appid Error!
			$echoData->result=1;
			$echoData->data='nav id 错误！';
			echo json_encode($echoData);
			return false;
		}
		$content=$this->dbHandler->selectPartData('content','navid_content',$_GET['navid']);
		$content=$content[0];
		$data=new stdClass;
		$data->navid=$content->navid_content;
		$data->text=$content->text_content;
		$echoData->result=0;
		$echoData->data=$data;
		echo json_encode($echoData);
	}
	public function formlist(){
		$echoData=new stdClass;
		if(!isset($_GET['navid']) || !is_numeric($_GET['navid'])){
			//1->Appid Error!
			$echoData->result=1;
			$echoData->data='nav id 错误！';
			echo json_encode($echoData);
			return false;
		}
		$forms=$this->dbHandler->SDUNR('form',array("navid_form"=>$_GET['navid']),array());
		$data=array();
		foreach($forms as $f){
			$form=new stdClass;
			$form->id=$f->id_form;
			$form->name=$f->name_form;
			$form->type=$f->type_form;
			$data[]=$form;
		}
		$echoData->result=0;
		$echoData->data=$data;
		echo json_encode($echoData);
	}
	public function formdata(){
		$echoData=new stdClass;
		$time=date("Y-m-d H:i:s");
		$message='提交時間：'.$time.'<br>';
		$data=json_decode($_POST['data']);
		$device=$data->device;
		foreach($data->formdata as $value){
			$form=$this->dbHandler->selectPartData('form','id_form',$value->formid);
			$itemName=$form[0]->name_form;
			$navId=$form[0]->navid_form;
			$message.=$itemName.':'.$value->value.'<br>';
			$info=array(
				"formid_formdata"=>$value->formid,
				"data_formdata"=>$value->value,
				"userid_formdata"=>0,
				"time_formdata"=>$time
			);
			$result=$this->dbHandler->insertdata("formdata",$info);
		}
		$nav=$this->dbHandler->selectPartData('nav','id_nav',$navId);
		$app=$this->dbHandler->selectPartData('app','id_app',$nav[0]->app_id_nav);
		$merchant=$this->dbHandler->selectPartData('merchant','id_merchant',$app[0]->merchant_id_app);
		$this->email($merchant[0]->email_merchant,'有用戶提交信息',$message);
		$echoData->result=0;
		echo json_encode($echoData);
	}
	public function sliderlist(){
		$echoData=new stdClass;
		if(!isset($_GET['appid']) || !is_numeric($_GET['appid'])){
			//1->Appid Error!
			$echoData->result=1;
			$echoData->data='app id 错误！';
			echo json_encode($echoData);
			return false;
		}
		$sliders=$this->dbHandler->SDUNR('homeslider',array("appid_homeslider"=>$_GET['appid']),array("col"=>'ordernum_homeslider',"by"=>'ASC'));
		$data=array();
		foreach($sliders as $s){
			$slider=new stdClass;
			$slider->src='http://clinic.coolkeji.com'.$s->src_homeslider;
			$data[]=$slider;
		}
		$echoData->result=0;
		$echoData->data=$data;
		echo json_encode($echoData);
	}
	public function update35(){
		echo '<?xml version="1.0" encoding="utf-8"?>'.
				'<info>'.
				'<version>1.1</version>'.
				'<url>http://clinic.coolkeji.com/uploads/med.apk</url>'.
				'<description>检查到最新版本，请及时更新！</description>'.
				'</info>';
	}
	
	public function update36(){
		echo '<?xml version="1.0" encoding="utf-8"?>'.
				'<info>'.
				'<version>1.0</version>'.
				'<url>http://clinic.coolkeji.com/uploads/pskin.apk</url>'.
				'<description>检查到最新版本，请及时更新！</description>'.
				'</info>';
	}
	public function update37(){
		echo '<?xml version="1.0" encoding="utf-8"?>'.
				'<info>'.
				'<version>1.1</version>'.
				'<url>http://clinic.coolkeji.com/uploads/echamp.apk</url>'.
				'<description>检查到最新版本，请及时更新！</description>'.
				'</info>';
	}
	public function update38(){
		echo '<?xml version="1.0" encoding="utf-8"?>'.
				'<info>'.
				'<version>1.1</version>'.
				'<url>http://clinic.coolkeji.com/uploads/wbeauty.apk</url>'.
				'<description>检查到最新版本，请及时更新！</description>'.
				'</info>';
	}
	public function update40(){
		echo '<?xml version="1.0" encoding="utf-8"?>'.
				'<info>'.
				'<version>1.1</version>'.
				'<url>http://clinic.coolkeji.com/uploads/vigor.apk</url>'.
				'<description>检查到最新版本，请及时更新！</description>'.
				'</info>';
	}
	public function get_info(){
		$msg="";
		switch($_POST['info_type']){
			case "nav"://1://固定页面2://固定二级页面3://文章列表4://表单页5://商城6://链接
				$navid=$_POST['navid'];
				$nav=$this->dbHandler->selectPartData('nav','id_nav',$navid);
				$nav=$nav[0];
				switch($nav->type_nav){
					case 1:
						$data=$this->dbHandler->selectPartData('content','navid_content',$navid);
						$nav->content=$data[0]->text_content;
					break;
					case 2:
						$subnavs=$this->dbHandler->SDUNR('subnav',array("navid_subnav"=>$navid),array("col"=>'id_subnav',"by"=>'asc'));
						$nav->subnavs=$subnavs;
					break;
					case 3:
						$essays=$this->dbHandler->SDUNR('essay',array("navid_essay"=>$navid),array("col"=>'lasttime_essay',"by"=>'desc'));
						$nav->essays=$essays;
					break;
					case 4:
						$forms=$this->dbHandler->SDUNR('form',array("navid_form"=>$navid),array("col"=>'id_form',"by"=>'asc'));
						$nav->forms=$forms;
					break;
					case 6:
						$links=$this->dbHandler->SDUNR('link',array("navid_link"=>$navid),array("col"=>'id_link',"by"=>'asc'));
//						$nav->link=file_get_contents($links[0]->url_link);
						$nav->link=$links[0]->url_link;
					break;
					case 5:
						if($nav->hasmallcat_nav==1){//有分类
							$categorys=$this->dbHandler->SDUNR('mall_category',array("navid_mall_category"=>$navid),array("col"=>'order_mall_category',"by"=>'asc'));
		/*					foreach($category as $key=>$c){
								$products=$this->dbHandler->SDUNR('product',array("catid_product"=>$c->id_mall_category),array("col"=>'id_product',"by"=>'asc'));
								$c->products=$products;
							}*/
							$nav->categorys=$categorys;
						}else{
							$products=$this->dbHandler->SDUNR('product',array("navid_product"=>$navid),array("col"=>'lasttime_product',"by"=>'desc'));
							$nav->products=$products;
						}
					break;
				}
				$msg=$nav;
			break;
			case "subnav":
				$subnavid=$_POST['subnavid'];
				$subnav=$this->dbHandler->selectPartData('subnav','id_subnav',$subnavid);
				$subnav=$subnav[0];
				$msg=$subnav;
			break;
			case "essay":
				$essayid=$_POST['essayid'];
				$essay=$this->dbHandler->selectPartData('essay','id_essay',$essayid);
				$essay=$essay[0];
				$msg=$essay;
			break;
			case "category_product":
				$categoryid=$_POST['categoryid'];
				$products=$this->dbHandler->SDUNR('product',array("catid_product"=>$categoryid),array("col"=>'lasttime_product',"by"=>'desc'));
				$msg=$products;
			break;
			case "product":
				$productid=$_POST['productid'];
				$product=$this->dbHandler->selectPartData('product','id_product',$productid);
				$msg=$product[0];
			break;
		}
		echo json_encode(array("result"=>"success","message"=>$msg));
	}
	public function add_formdata(){
		$userid=isset($_SESSION["appuserid"])?$_SESSION["appuserid"]:0;
		$time=date("Y-m-d H:i:s");
		$message='提交時間：'.$time.'<br>';
//		$message.='用戶ID：'.$userid.'<br>';
		foreach($_POST['data'] as $value){
			$form=$this->dbHandler->selectPartData('form','id_form',$value["formid"]);
			$itemName=$form[0]->name_form;
			$message.=$itemName.':'.$value["data"].'<br>';
			$info=array(
				"formid_formdata"=>$value["formid"],
				"data_formdata"=>$value["data"],
				"userid_formdata"=>$userid,
				"time_formdata"=>$time
			);
			$result=$this->dbHandler->insertdata("formdata",$info);
		}
		$app=$this->dbHandler->selectPartData('app','id_app',$_GET['appid']);
		$merchant=$this->dbHandler->selectPartData('merchant','id_merchant',$_SESSION['userid']);
		$this->email($merchant[0]->email_merchant,'由用戶提交信息',$message);
		echo json_encode(array("result"=>"success","message"=>"信息寫入成功"));
	}
	public function check_push_msg(){
		$wherein[0]=array("orfield"=>"appid_message","ordata"=>array($_GET["appid"],0));
		$wherein[1]=array("orfield"=>"type_message","ordata"=>array("0","2"));
		$wherein[2]=array("orfield"=>"device_message","ordata"=>array("0",$_GET["device"]=="android"?1:2));
		$order=array("col"=>"time_message","by"=>"asc");
//		$msg=$this->dbHandler->SDSDUNROR("message",$condition,$orfield,$ordata,$order);
		$msg=$this->dbHandler->msgSelect("message",$wherein,$order);
		foreach($msg as $m){
			if(strtotime(date("Y-m-d H:i:s"))-strtotime($m->time_message)<120){
				echo json_encode(array("title"=>$m->title_message,"message"=>$m->msg_message));
			}else echo "";
		}
//		echo json_encode(array("title"=>"放假了","message"=>"赶紧回家"));
	}	
	public function email($to,$subject,$message){
		$this->load->library('email');
		//以下设置Email参数
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'smtp.163.com';
		$config['smtp_user'] = 'sunxguo@163.com';
		$config['smtp_pass'] = '19910910Mk1024';
		$config['smtp_port'] = '25';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		//以下设置Email内容
		$this->email->from('sunxguo@163.com', 'KM AppPlatform');
		$this->email->to($to); 
	//			$this->email->cc('another@another-example.com'); 
	//			$this->email->bcc('them@their-example.com'); 

		$this->email->subject($subject);
		$this->email->message($message); 

		$this->email->send();

	//			echo $this->email->print_debugger();
	}
}

/* End of file home.php */
/* Location: ./application/controllers/mobile/home.php */
