<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;


class Things extends ActiveRecord {
	public static function getDb() {
     	return Yii::$app->db;
 	}	

}

?>