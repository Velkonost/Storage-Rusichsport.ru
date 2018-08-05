<?php


/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
	use yii\bootstrap\Nav;
	use yii\bootstrap\NavBar;
	use yii\widgets\Breadcrumbs;
	use app\assets\AppAsset;

$this->title = 'Storage';
if(isset($_GET['log'])){
	Yii::$app->response->cookies->remove('cook');
}
if (Yii::$app->getRequest()->getCookies()->has('cook')) {
			Yii::$app->response->redirect('storage');
}
?>
<div class="site-index">
	<div class="body-content">
		
			<div class="jumbotron">
			<div class="goCenter">
				<p style="">Заполните все поля, чтобы авторизоваться:</p>
			</div>
				<?php $form = ActiveForm::begin([
					'id' => 'login-form',
					'layout' => 'horizontal',
					'fieldConfig' => [
						'labelOptions' => ['style' => ''],
					],
				]); ?>

 					<?= $form->field($model, 'username')->textInput(['autofocus' => true, 'style'=>'width:40%;' ])->label('Имя пользователя', ['style'=>'margin-left:20%'])?>

					<?= $form->field($model, 'password')->passwordInput([ 'style'=>'width:40%;' ])->label('Пароль', ['style'=>'margin-left:20%']) ?>

				<div class="goCenter2">
					<div class="form-group">
						<div class="col-lg-offset-1 col-lg-11">
							<?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button', 'style'=>'margin-top:1%; width:20%; height:5%']) ?>
						</div>
					</div>

				<?php ActiveForm::end(); ?>
				</div>
			
		</div>
	</div>
</div>

<style>
	.goCenter{
		margin-left:30%;
	}
	.goCenter2{
		margin-left:40%;
	}
</style>
