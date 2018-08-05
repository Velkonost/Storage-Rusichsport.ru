<?php

namespace app\models;


use Yii;
use yii\base\Model;

class AddThingForm extends Model {

	public $category;
	public $name;
	public $s;
	public $m;
	public $l;
	public $xl;
	public $xxl;
	public $xxxl;
	public $x4xl;
	public $x5xl;
	public $amount;
	public $price;


	public function rules() {
		return [
			[['name', 's', 'm', 'l', 'xl', 'xxl', 'xxxl', 'x4xl', 'x5xl', 'amount', 'price'], 'required'],
		];
	}

}

?>