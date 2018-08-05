<?php
	namespace app\models;

	use Yii;
	use yii\base\Model;
	
	class EditForm extends Model
	{
		public $editNames = [], $editAmounts = [];
		public $editCategory = [], $editSs = [], $editMs = [], $editLs = [], $editXls = [], $editXxls = [], $editXxxls = [], $editx4xls = [], $editx5xls = [], $editPrices = [], $editArticle = [];
		
		public function rules(){
			return [
				// username and password are both required
				[['editNames', 'editSs', 'editMs', 'editLs', 'editXls', 'editXxls', 'editXxxls', 'editx4xls', 'editx5xls', 'editPrices', 'editArticle'], 'required',  'message'=>''],
				// rememberMe must be a boolean value
				// ['names', 'default', message => ''],
				// ['ss', 'number', message => ''],
				// ['ms', 'number', message => ''],
				// ['ls', 'number', message => ''],
				// ['xls', 'number', message => ''],
				// ['xxls', 'number', message => ''],
				// ['xxxls', 'number', message => ''],
				// ['prices', 'number', message => ''],
				// ['category','default', message=>'']
			];
		}
		
	}
?>
