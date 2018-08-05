<?php
	use yii\helpers\Json;
	use yii\web\Controller;
	use app\models\Leads;
	if(!empty($_GET['contact_name']) || !empty($_GET['phone']) || !empty($_GET['city'])){
		$date = $_GET['date'];
		$date2 = $_GET['date2'];

		$date_create_first = $_GET['date_create_first'];
		$date_create_second = $_GET['date_create_second'] + 86400;

		$date_close_first = $_GET['date_close_first'];
		$date_close_second = $_GET['date_close_second'] + 86400;

		$date_send_first = $_GET['date_send_first'];
		$date_send_second = $_GET['date_send_second'] + 86400;

		$date_dost_first = $_GET['date_dost_first'];
		$date_dost_second = $_GET['date_dost_second'] + 86400;

		$dateSuc_dost_first = $_GET['dateSuc_dost_first'];
		$dateSuc_dost_second = $_GET['dateSuc_dost_second'] + 86400;

		$date_return_first = $_GET['date_return_first'];
		$date_return_second = $_GET['date_return_second'] + 86400;

		$date_return_thing_first = $_GET['date_return_thing_first'];
		$date_return_thing_second = $_GET['date_return_thing_second'] + 86400;

		$isDateCreate = $date_create_first == -1 ? false : true;
		$isDateClose = $date_close_first == -1 ? false : true;
		$isDateSend = $date_send_first == -1 ? false : true;
		$isDateDost = $date_dost_first == -1 ? false : true;
		$isDateSucDost = $dateSuc_dost_first == -1 ? false : true;
		$isDateReturn = $date_return_first == -1 ? false : true;
		$isDateReturnThing = $date_return_thing_first == -1 ? false : true;

		$get = Yii::$app->db->createCommand("SELECT * FROM contacts 
				WHERE `name` LIKE '%$_GET[contact_name]%'
				AND `phone` LIKE '%$_GET[phone]%'
				")->queryAll();

		$data = [];
		$resultData = [];
		//echo 1;
		$g = 0;
		$gg = 0;


		for($i = 0; $i<count($get); $i++){

			$id = $get[$i]['contact_id'];
			
			$data[$g]['id'] = $get[$i]['contact_id'];
			$data[$g]['name'] = $get[$i]['name'];
			$data[$g]['phone'] = $get[$i]['phone'];
			$data[$g]['city'] = $get[$i]['city'];

			$get2 = Yii::$app->db->createCommand("SELECT * FROM leads 
				WHERE `critical_acc` LIKE '%$_GET[critical_acc]%'
				AND `contact_id` LIKE '%$id%'
				AND `city` LIKE '%$_GET[city]%'
				AND `lead_status` LIKE '%$_GET[status]%'
				AND `lead_summa` LIKE '%$_GET[price]%'
				AND `sdek_summa` LIKE '%$_GET[sdek_summa]%'
				ORDER BY lead_date_create DESC
				")->queryAll();

			for($j = 0; $j<count($get2); $j++) {
				if(
					(!$isDateCreate || ($get2[$j]['lead_date_create']>= $date_create_first && $get2[$j]['lead_date_create']<=$date_create_second))
					&& (!$isDateClose || ($get2[$j]['lead_date_close']>= $date_close_first && $get2[$j]['lead_date_close']<=$date_close_second))
					&& (!$isDateSend || ($get2[$j]['lead_date_send']>= $date_send_first && $get2[$j]['lead_date_send']<=$date_send_second))
					&& (!$isDateDost || ($get2[$j]['lead_date_delivered']>= $date_dost_first && $get2[$j]['lead_date_delivered']<=$date_dost_second))
					&& (!$isDateSucDost || ($get2[$j]['lead_date_success_delivered']>= $dateSuc_dost_first && $get2[$j]['lead_date_success_delivered']<=$dateSuc_dost_second))
					&& (!$isDateReturn || ($get2[$j]['lead_date_reset']>= $date_return_first && $get2[$j]['lead_date_reset']<=$date_return_second))
					&& (!$isDateReturnThing || ($get2[$j]['lead_date_reset_thing']>= $date_return_thing_first && $get2[$j]['lead_date_reset_thing']<=$date_return_thing_second))
				){

					$resultData[$gg]['id'] = $get2[$j]['lead_id'];
					$resultData[$gg]['status'] = $get2[$j]['lead_status'];
					$resultData[$gg]['main'] = $get2[$j]['critical_acc'];
					$resultData[$gg]['name'] = $data[$g]['name'];
					$resultData[$gg]['phone'] = $data[$g]['phone'];
					$resultData[$gg]['city'] = $get2[$j]['city'];
					$resultData[$gg]['lead_date_create'] = $get2[$j]['lead_date_create'];
					$resultData[$gg]['lead_date_close'] = $get2[$j]['lead_date_close'];
					$resultData[$gg]['lead_date_send'] = $get2[$j]['lead_date_send'];
					$resultData[$gg]['lead_date_delivered'] = $get2[$j]['lead_date_delivered'];
					$resultData[$gg]['lead_date_success_delivered'] = $get2[$j]['lead_date_success_delivered'];
					$resultData[$gg]['lead_date_reset'] = $get2[$j]['lead_date_reset'];
					$resultData[$gg]['lead_date_reset_thing'] = $get2[$j]['lead_date_reset_thing'];
					$resultData[$gg]['lead_summa'] = $get2[$j]['lead_summa'];
					$resultData[$gg]['sdek_summa'] = $get2[$j]['sdek_summa'];

					$gg ++;
				}
			}
				 $g++;
		}
		echo json_encode($resultData);
	} else {

		$date_create_first = $_GET['date_create_first'];
		$date_create_second = $_GET['date_create_second'] + 86400;

		$date_close_first = $_GET['date_close_first'];
		$date_close_second = $_GET['date_close_second'] + 86400;

		$date_send_first = $_GET['date_send_first'];
		$date_send_second = $_GET['date_send_second'] + 86400;

		$date_dost_first = $_GET['date_dost_first'];
		$date_dost_second = $_GET['date_dost_second'] + 86400;

		$dateSuc_dost_first = $_GET['dateSuc_dost_first'];
		$dateSuc_dost_second = $_GET['dateSuc_dost_second'] + 86400;

		$date_return_first = $_GET['date_return_first'];
		$date_return_second = $_GET['date_return_second'] + 86400;

		$date_return_thing_first = $_GET['date_return_thing_first'];
		$date_return_thing_second = $_GET['date_return_thing_second'] + 86400;

		$isDateCreate = $date_create_first == -1 ? false : true;
		$isDateClose = $date_close_first == -1 ? false : true;
		$isDateSend = $date_send_first == -1 ? false : true;
		$isDateDost = $date_dost_first == -1 ? false : true;
		$isDateSucDost = $dateSuc_dost_first == -1 ? false : true;
		$isDateReturn = $date_return_first == -1 ? false : true;
		$isDateReturnThing = $date_return_thing_first == -1 ? false : true;

		$data = [];
		$resultData = [];
		//echo 1;
		$g = 0;
		$gg = 0;

		$get2 = Yii::$app->db->createCommand("SELECT * FROM leads 
				WHERE `critical_acc` LIKE '%$_GET[critical_acc]%'
				AND `lead_status` LIKE '%$_GET[status]%'
				AND `lead_summa` LIKE '%$_GET[price]%'
				AND `sdek_summa` LIKE '%$_GET[sdek_summa]%'
				ORDER BY lead_date_create DESC
				")->queryAll();

		for($j = 0; $j<count($get2); $j++){
			if(
				(!$isDateCreate || ($get2[$j]['lead_date_create']>= $date_create_first && $get2[$j]['lead_date_create']<=$date_create_second))
					&& (!$isDateClose || ($get2[$j]['lead_date_close']>= $date_close_first && $get2[$j]['lead_date_close']<=$date_close_second))
					&& (!$isDateSend || ($get2[$j]['lead_date_send']>= $date_send_first && $get2[$j]['lead_date_send']<=$date_send_second))
					&& (!$isDateDost || ($get2[$j]['lead_date_delivered']>= $date_dost_first && $get2[$j]['lead_date_delivered']<=$date_dost_second))
					&& (!$isDateSucDost || ($get2[$j]['lead_date_success_delivered']>= $dateSuc_dost_first && $get2[$j]['lead_date_success_delivered']<=$dateSuc_dost_second))
					&& (!$isDateReturn || ($get2[$j]['lead_date_reset']>= $date_return_first && $get2[$j]['lead_date_reset']<=$date_return_second))
					&& (!$isDateReturnThing || ($get2[$j]['lead_date_reset_thing']>= $date_return_thing_first && $get2[$j]['lead_date_reset_thing']<=$date_return_thing_second))
			){
				$contact_id = $get2[$j]['contact_id'];
				
				$get = Yii::$app->db->createCommand("SELECT * FROM contacts 
					WHERE `contact_id` = '$contact_id'
					")->queryOne();
				
				$resultData[$gg]['id'] = $get2[$j]['lead_id'];
				$resultData[$gg]['status'] = $get2[$j]['lead_status'];
				$resultData[$gg]['main'] = $get2[$j]['critical_acc'];
				if ($contact_id) {
					$resultData[$gg]['name'] = $get['name'];
					$resultData[$gg]['phone'] = $get['phone'];
				} else {
					$resultData[$gg]['name'] = '';
					$resultData[$gg]['phone'] = '';
				}
				$resultData[$gg]['city'] = $get2[$j]['city'];
				$resultData[$gg]['lead_date_create'] = $get2[$j]['lead_date_create'];
				$resultData[$gg]['lead_date_close'] = $get2[$j]['lead_date_close'];
				$resultData[$gg]['lead_date_send'] = $get2[$j]['lead_date_send'];
				$resultData[$gg]['lead_date_delivered'] = $get2[$j]['lead_date_delivered'];
				$resultData[$gg]['lead_date_success_delivered'] = $get2[$j]['lead_date_success_delivered'];
				$resultData[$gg]['lead_date_reset'] = $get2[$j]['lead_date_reset'];
				$resultData[$gg]['lead_date_reset_thing'] = $get2[$j]['lead_date_reset_thing'];
				$resultData[$gg]['lead_summa'] = $get2[$j]['lead_summa'];
				$resultData[$gg]['sdek_summa'] = $get2[$j]['sdek_summa'];

				$gg ++;
			}
		}
		echo json_encode($resultData);
	}
?>