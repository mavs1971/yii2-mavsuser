<?php

use kartik\alert\AlertBlock;
use dosamigos\datepicker\DatePicker;
use abhimanyu\installer\helpers\enums\Configuration as Enum;

/** @var $model \abhimanyu\user\models\User */
/** @var $this \yii\web\View */
/** @var $success */

$this->title = yii::t('app','Sign Up - ') . Yii::$app->config->get(Enum::APP_NAME);

echo AlertBlock::widget([
	'delay'           => 5000,
	'useSessionFlash' => TRUE
]);
?>

<div class="container" style="max-width: 400px;margin: 0 auto 20px;text-align: left;">
	<div class="panel panel-default">
		<div class="panel-heading"><?= Yii::t('app', 'Sign Up!') . '  Socios Juridicos'?></div>

		<div class="panel-body">
			<?php $form = \yii\widgets\ActiveForm::begin([
				'id'                   => 'register-form',
				'enableAjaxValidation' => FALSE,
			]); ?>

			<div class="form-group">
				<?= $form->field($model, 'numero_accion')->textInput([
					'class'        => 'form-control',
					'label'		=>'label',
					'autocomplete' => 'off',
					'autofocus'    => 'on',
					'onblur'=>'toAccion(this);',
				]) ?>
			</div>

			<div class="form-group">
				<?= $form->field($model, 'rif')->textInput([
					'class'        => 'form-control',
					'autocomplete' => 'off',
				]) ?>
			</div>

			<div class="form-group">
				<?= $form->field($model, 'nombres')->textInput([
					'class'        => 'form-control',
					'autocomplete' => 'off',
					'onblur'=>'toUpper(this);',
				]) ?>
			</div>

			<div class="form-group">
				<?= $form->field($model, 'email')->textInput([
					'class'        => 'form-control',
					'autocomplete' => 'off'
				]) ?>
			</div>
                    
                        <div class="form-group">
				<?= $form->field($model, 'email_confirm')->textInput([
					'class'        => 'form-control',
					'autocomplete' => 'off'
				]) ?>
			</div>

			<div class="form-group">
				<?= $form->field($model, 'username')->textInput([
					'class'        => 'form-control',
					'autocomplete' => 'off',
				]) ?>
			</div>

			<div class="form-group">
				<?= $form->field($model, 'password')->passwordInput([
					'class' => 'form-control',
				]) ?>
			</div>

			<div class="form-group">
				<?= $form->field($model, 'password_confirm')->passwordInput([
					'class' => 'form-control',
				]) ?>
			</div>

		<?php if (!empty($errorMsg)) { ?>
			<div class="alert alert-danger">
				<strong><?= $errorMsg ?></strong>
			</div>
		<?php } ?>


			<hr>
			<div class="row">
				<div class="col-md-4">
					<?= \yii\helpers\Html::submitButton(Yii::t('app', 'Juridico'), ['class' => 'btn btn-primary']) ?>
				</div>
			</div>

			<?php \yii\widgets\ActiveForm::end(); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
function toUpper(mystring) {
		strval = mystring.value;
		var sp = strval.split(' ');
		var wl=0;
		var f ,r;
		var word = new Array();
		for (i = 0 ; i < sp.length ; i ++ ) {
		f = sp[i].substring(0,1).toUpperCase();
		r = sp[i].substring(1);
		word[i] = f+r;
		}
		newstring = word.join(' ');
		document.getElementById(mystring.id).value = strval.toUpperCase();
		return true;   
	}

function toAccion(mystring) {
		if (mystring.value < 100000){
		var strval2 ='';
		var strval ='';
		var strval = '0000' + mystring.value;
		var strval2 = strval.substr(-5);
		document.getElementById(mystring.id).value = strval2;
	}
		return true;   
	}


</script>

