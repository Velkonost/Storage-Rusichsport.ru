<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;


class Deals extends ActiveRecord {
	public static function getDb() {
     	return Yii::$app->db;
 	}	

}

?>