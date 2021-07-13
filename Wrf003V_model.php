<?php
/** 
  * @version 1.0
  * @author Siti Nabilah Huda binti Razak
  * @required Wrf003V_model
*/
defined('BASEPATH') OR exit('No direct script access allowed');
/*--------------------------------------------------------------
  @Load Wrf003V_model 
  --------------------------------------------------------------*/

class Wrf003V_model extends CI_Model {

	private $staff_id;
    public function __construct() {
        $this->load->database();
		$this->staff_id = $this->lib->userid();
    }

/*--------------------------------------------------------------
  @operation get invoice(first page)
 ---------------------------------------------------------------*/	
	public function getInvoiceDetail($deptCode, $territoryCode){
		if($deptCode==NULL && $territoryCode!=NULL){
		$query="select *
		from cinvoice_head
		where ch_status = 'ENTRY'
		and ch_cust_type ='$deptCode'
		and substr(ch_invoice_no,1,6)= '$territoryCode'";
		$del=$this->db->query($query);
		return $del->result_case('UPPER');
		}
		if($deptCode!=NULL && $territoryCode==NULL){
		$query="select *
		from cinvoice_head
		where ch_status = 'ENTRY'
		and ch_cust_type ='$deptCode'
		and substr(ch_invoice_no,1,6)= '$territoryCode'";
		$del=$this->db->query($query);
		return $del->result_case('UPPER');
		}
		if($deptCode!=NULL && $territoryCode!=NULL){
		$query="select *
		from cinvoice_head
		where ch_status = 'ENTRY'
		and ch_cust_type ='$deptCode'
		and substr(ch_invoice_no,1,6)='$territoryCode'";
		$del=$this->db->query($query);
		return $del->result_case('UPPER');
		}
		if($deptCode==NULL && $territoryCode==NULL){
		$query="select *
		from cinvoice_head
		where ch_status = 'ENTRY'
		and ch_cust_type ='$deptCode'
		and substr(ch_invoice_no,1,6)= '$territoryCode'";
		$del=$this->db->query($query);
		return $del->result_case('UPPER');
		}
	
		
	}

/*--------------------------------------------------------------
  @operation get invoice
 ---------------------------------------------------------------*/	
	public function getInv() {
		
       $this->db->select('ch_cust_type');
	   $this->db->from('cinvoice_head');
	   $this->db->order_by('ch_cust_type ASC');
	   $this->db->where('ch_cust_type !=','STUD');
	   return $this->db->get()->result_case('UPPER');
    }

/*--------------------------------------------------------------
  @operation get date invoice
 ---------------------------------------------------------------*/
	public function getInvDate() {
		
       $this->db->select('ch_invoice_date');
	   $this->db->from('cinvoice_head');
	   $this->db->order_by('ch_invoice_date DESC');
	   $this->db->where('ch_cust_type !=','STUD');
	   $this->db->where('ch_status','ENTRY');
	   return $this->db->get()->result_case('UPPER');
    }

/*--------------------------------------------------------------
  @operation get data invoice
 ---------------------------------------------------------------*/
	public function getInvID($invID){
		
       $this->db->select('*');
	   $this->db->from('cinvoice_head');
	   $this->db->where('ch_invoice_no',$invID);
	   return $this->db->get()->row_case('UPPER');
    }
	
/*--------------------------------------------------------------
  @operation get detail
 ---------------------------------------------------------------*/
	public function getDetail($invID){
		
       $this->db->select('*');
	   $this->db->from('cinvoice_detl');
	   $this->db->where('cd_invoice_no',$invID);
	   return $this->db->get()->result_case('UPPER');
    }

/*--------------------------------------------------------------
  @operation get updte verify
 ---------------------------------------------------------------*/
	public function update($id,$new_status,$verifyDate,$todayDate,$time){
		
            $data = array(
				'ch_status' => $new_status,
				'ch_verify_by' => $this->staff_id,
                );
				
				$a=$verifyDate." ".$time;
				
				$t1="timestamp'$a'";
				$this->db->set("ch_verify_date",$t1,false);
				
				//$trans="TO_DATE('".$todayDate."','yyyy-mm-dd')";
				$b=$todayDate." ".$time;
				$t2="timestamp'$b'";
				$this->db->set("ch_trans_verify_date",$t2,false);
				
				/* $verifyDate2="TO_DATE('".$verifyDate."','DD/MM/YYYY')";
				$this->db->set("CH_VERIFY_DATE",$verifyDate2,false);
				
				$trans="TO_DATE('".$todayDate."','DD/MM/YYYY')";
				$this->db->set("CH_TRANS_VERIFY_DATE",$trans,false); */

                $this->db->where('ch_invoice_no', $id);
				$detail=$this->db->update('cinvoice_head', $data);
				if($detail==true)
				{
					return 1;
				}else {
					return 0;
					}
	}

/*--------------------------------------------------------------
  @operation get detail
 ---------------------------------------------------------------*/
	public function getInvoiceDetail3($type_id,$doc_id) {
       $this->db->select('*');
	   $this->db->from('cinvoice_detl');
	   $this->db->where('cd_invoice_no=',$type_id);
	   $this->db->where('cd_seq_no=',$doc_id );
	   return $this->db->get()->row_case('UPPER');
    }
	
	public function getTimeSys() {
        $query="SELECT TO_CHAR
			(now(), 'HH24:MI:SS') as NOW";
        $del=$this->db->query($query);
        return $del->row_case('UPPER')->NOW;
    }

}
//---------------------------------------------------------------
// @end of process
// @22/2/2019
//---------------------------------------------------------------
?>