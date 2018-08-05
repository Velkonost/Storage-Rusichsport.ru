<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;


class Meta extends ActiveRecord {
	public static function getDb() {
     	return Yii::$app->db;
 	}	

}

?>