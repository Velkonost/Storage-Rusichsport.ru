<?php
namespace app\models;
use yii\db\ActiveRecord;
use Yii;
class Contacts extends ActiveRecord {
	public static function getDb() {
     	return Yii::$app->db;
 	}	
}
?>