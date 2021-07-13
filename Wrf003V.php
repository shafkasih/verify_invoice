<?php
/** 
  * @version 1.0
  * @include index(), applicationList(), viewTabFilter(), detlApplication(),verify(),check(),viewDetail()
  * @author Siti Nabilah Huda binti Razak
  * @required Wrf003V.php
*/
defined('BASEPATH') OR exit('No direct script access allowed');
/*--------------------------------------------------------------
  @Load Wrf003V_model for connection data in DB 
  @Location (MODULES/ACCTRECEIVABLE/MODELS/WRF003V_MODEL)
  --------------------------------------------------------------*/
	class Wrf003V extends MY_Controller {
	private $staff_id;
    function __construct() {
        parent::__construct();
        $this->load->model('Wrf003V_model','fee');
		$this->staff_id = $this->lib->userid();
    }

/*--------------------------------------------------------------
  @Run funtion index() for view main page with searching
  @operation get parameter date and cust type for parsing to applicationList
  --------------------------------------------------------------*/
	public function index($program=null,$date=null){
		$this->session->set_userdata('referer',current_url());
		 $this->session->set_userdata('usDept','');
        $this->session->set_userdata('usTerritory','');
		
		if (empty($program)) {
			if (!empty($this->session->usDept)) {
				$program = $this->session->usDept;
			} else {
				$program=NULL;
			}
       	}
        $data['default_type'] = $program;

    	        
        if (empty($date)) {
			if (!empty($this->session->usTerritory)) {
				$date = $this->session->usTerritory;
			} 
			else {
				$date=NULL;
			}
       	}
		$data['program'] = $this->dropdown($this->fee->getInv(),'CH_CUST_TYPE','CH_CUST_TYPE','---Please Select---');
		
		$this->render($data);
	}

/*--------------------------------------------------------------
  @Run funtion applicationList() view table of invoice
  @operation view invoice 
  --------------------------------------------------------------*/
	public function applicationList(){
		// get parameter values
		$type_id = $this->input->post('dCode',true);	
		$dtINT = $this->input->post('tCode',true);
		
		$parmID= 'BL' . substr($dtINT,2,2). substr($dtINT,5,2);
		// get available records
		$data['programs'] = $this->fee->getInvoiceDetail($type_id,$parmID);
		
        $this->renderAjax($data);
    }// applicationList()
	
/*--------------------------------------------------------------
  @Run funtion viewTabFilter() 
  @operation sorting data 
  --------------------------------------------------------------*/
	public function viewTabFilter($tabID) {
		// set session
		$this->session->set_userdata('tabID', $tabID);
		
        redirect($this->class_uri('index'));
    }//viewTabFilter()

/*--------------------------------------------------------------
  @Run funtion detlApplication() 
  @operation view detail invoice using No BL
  --------------------------------------------------------------*/
	public function detlApplication(){
		// get parameter values
		$invID = $this->input->post('invoiceID',true);
		
		$data['invoiceID'] = $invID;
		$data['P'] = $this->fee->getInvID($invID);
		$data['doc_rec'] = $this->fee->getDetail($invID);
		
        $this->renderAjax($data);
    }//delApplication()

/*--------------------------------------------------------------
  @Run funtion verify() 
  @operation verify processing
  --------------------------------------------------------------*/
	    public function verify(){
		$this->isAjax();
        $id = $this->input->post('inv_id2',true);
        $todayDate = $this->input->post('todayDate',true);
        $todayDate2 = $this->input->post('todayDate2',true);
        $new_status = $this->input->post('new_status',true);
        $backdate = $this->input->post('backdate',true);
		$time=$this->fee->getTimeSys();
		
		$idInvs=substr($id,1,11);
        
		if($backdate==NULL){
			
			$todayM=substr($todayDate,2,2);
			$invM=substr($id,5,2);
			
			$todayY=substr($todayDate,6,2);
			$invY=substr($id,3,2);
			
			$convertM=$todayM-$invM;
			$convertY=$todayY-$invY;
		
			if($convertY==0)
			{
				if($convertM==0)
				{
						if($backdate==NULL)
						{
							$verifyDate=$todayDate2;

						}
						else{
							
							$year=substr($backdate,0,4);
							$month=substr($backdate,5,2);
							$day=substr($backdate,8,2);
							
							$verifyDate=substr($backdate,0,4)."-". substr($backdate,5,2)."-".substr($backdate,8,2);
							
						}
						
						$update=$this->fee->update($idInvs,$new_status,$verifyDate,$todayDate2,$time);
						if($update==1){
							$json = array('sts' => 0, 'msg' => 'Success to Verify.');
						}
						else{
							$json = array('sts' => 1, 'msg' => 'Fail to Record, Please Contact Administator');
						}
				}else{
					$json = array('sts' => 1, 'msg' =>'Please insert Transaction Date:  ' .$idInvs );
				}
				}else{
					$json = array('sts' => 1, 'msg' =>'Please insert Transaction Date:  ' .$idInvs );
					}
		}	
		else if($backdate!=NULL){
					
				$todayM2=substr($backdate,5,2);
				$invM=substr($id,5,2);
				
				$todayY2=substr($backdate,2,2);
				$invY=substr($id,3,2);
				
				$convertM2=$todayM2-$invM;
				$convertY2=$todayY2-$invY;
				if($convertY2==0)
				{
					if($convertM2==0)
						{
							$verifyDate=substr($backdate,0,4)."-". substr($backdate,5,2)."-".substr($backdate,8,2);
							
						$update=$this->fee->update($idInvs,$new_status,$verifyDate,$todayDate2,$time);
						if($update==1){
							$json = array('sts' => 0, 'msg' => 'Success to Record.');
						}
						else{
							$json = array('sts' => 1, 'msg' => 'Fail to Record, Please Contact Administator');
						}
					}else{
						$json = array('sts' => 1, 'msg' =>'Please insert Transaction Date Properly:  ' .$idInvs );
					}
				}else{
					$json = array('sts' => 1, 'msg' =>'Please insert Transaction Date Properly:  ' .$idInvs );
					}
			}	
		
        echo json_encode($json);
		
		}//verify()
		
/*--------------------------------------------------------------
  @Run funtion check() 
  @operation for checking invoice
  --------------------------------------------------------------*/
	public function check(){
	$this->isAjax();
    $id = $this->input->post('inv_id2',true);
    $todayDate = $this->input->post('todayDate',true);
    $new_status = $this->input->post('new_status',true);
    $backdate = $this->input->post('backdate',true);
		
	$idInvs=substr($id,1,11);
        
		if($backdate==NULL){
			
			$todayM=substr($todayDate,2,2);
			$invM=substr($id,5,2);
			
			$todayY=substr($todayDate,6,2);
			$invY=substr($id,3,2);
			
			$convertM=$todayM-$invM;
			$convertY=$todayY-$invY;
		
			if($convertY==0)
			{
				if($convertM==0)
				{
						
					$json = array('sts' => 1, 'msg' => 'Success to Verify.');
				}
				else{
					$json = array('sts' => 0, 'msg' => 'Please insert Posting Date  ' );
				}
			}else{
				$json = array('sts' => 0, 'msg' =>'Please insert Posting Date  ' );
			}
				
		}	
		else if($backdate!=NULL){
					
				$todayM2=substr($backdate,5,2);
				$invM=substr($id,5,2);
				
				$todayY2=substr($backdate,2,2);
				$invY=substr($id,3,2);
				
				$convertM2=$todayM2-$invM;
				$convertY2=$todayY2-$invY;
				if($convertY2==0)
				{
					if($convertM2==0)
						{
							$json = array('sts' => 1, 'msg' => 'Success to Record.');
						}
						else{
							$json = array('sts' => 0, 'msg' => 'Please insert Posting Date Properly  '  );
						}
					}else{
						$json = array('sts' => 0, 'msg' =>'Please insert Posting Date Properly  '  );
					}
				
			}	
		
        echo json_encode($json);
		
	}//check()
	
/*--------------------------------------------------------------
  @Run funtion viewDetail() 
  @operation for view Detail Invoice
  --------------------------------------------------------------*/
	public function viewDetail(){
		$type_id = $this->input->post('type_id',true);
		$doc_id = $this->input->post('doc_id',true);
		$data['P'] = $this->fee->getInvoiceDetail3($type_id,$doc_id);
		$this->render($data);
	}//viewDetail()
//---------------------------------------------------------------
// @end of process
// @22/2/2019
//---------------------------------------------------------------	

}
?>