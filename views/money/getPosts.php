<?php
	use yii\helpers\Json;
	use yii\web\Controller;
	use app\models\Leads;
	if(!empty($_GET['date']) && !empty($_GET['date2'])){
		$date = $_GET['date'];
		$date2 = $_GET['date2'];
		$get = Yii::$app->db->createCommand("SELECT * FROM leads ORDER BY lead_date_create DESC")
		->queryAll();
		$data = [];
		//echo 1;
		$g = 0;

		//echo $date;
		for($i = 0; $i<count($get); $i++){
			$id = $get[$i]['contact_id'];
			if($get[$i]['lead_date_create']>=$date && $get[$i]['lead_date_create']<=$date2){
				 $get2 = Yii::$app->db->createCommand("SELECT * FROM contacts WHERE contact_id = '$id'")
					->queryOne();
				 
				 $data[$g]['id'] = $get[$i]['lead_id'];
				 $data[$g]['status'] = $get[$i]['lead_status'];
				 $data[$g]['main'] = $get[$i]['critical_acc'];
				 $data[$g]['name'] = $get2['name'];
				 $data[$g]['phone'] = $get2['phone'];
				 $data[$g]['city'] = $get[$i]['city'];
				 $data[$g]['lead_date_create'] = $get[$i]['lead_date_create'];
				 $data[$g]['lead_date_close'] = $get[$i]['lead_date_close'];
				 $data[$g]['lead_date_send'] = $get[$i]['lead_date_send'];
				 $data[$g]['lead_date_delivered'] = $get[$i]['lead_date_delivered'];
				 $data[$g]['lead_date_success_delivered'] = $get[$i]['lead_date_success_delivered'];
				 $data[$g]['lead_date_reset'] = $get[$i]['lead_date_reset'];
				 $data[$g]['lead_date_reset_thing'] = $get[$i]['lead_date_reset_thing'];
				 $data[$g]['lead_summa'] = $get[$i]['lead_summa'];
				 $data[$g]['sdek_summa'] = $get[$i]['sdek_summa'];

				 $g++;
			}
		}

		echo json_encode($data);

	}else{
		$get = Yii::$app->db->createCommand("SELECT * FROM leads ORDER BY lead_date_create DESC")
		->queryAll();
		$data = [];
		//echo 1;
		$g= 0;
		
		$price_itog = 0;
		$sdek_itog = 0;

		for($i = 0; $i<count($get); $i++){
					
			$flag = false;
			for($f = count($data)-1; $f>=0; $f--){
				
				if($data[$f]['month']==gmdate('m', $get[$i]['lead_date_create']) && $data[$f]['year']==gmdate('Y', $get[$i]['lead_date_create'])){
					$flag = true;
				}else{
					
				}
			} 
			if(!$flag) {
				$data[$g]['month'] = gmdate('m', $get[$i]['lead_date_create']);
				$data[$g]['year'] = gmdate('Y', $get[$i]['lead_date_create']);
				$g++;
			}
		}
		echo json_encode($data);
	}
	//echo 1;
?>