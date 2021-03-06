<?php

use abhimanyu\installer\helpers\enums\Configuration as Enum;
use kartik\alert\AlertBlock;
use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
//use yii\widgets\ActiveForm;
//use Zelenin\yii\widgets\Recaptcha\widgets\Recaptcha;

/* @var $model \abhimanyu\user\models\AccountLoginForm */
/* @var $canRegister bool */
/* @var $canRecoverPassword bool */
/* @var $this \yii\web\View */

$this->title = 'Ingrese - ' . Yii::$app->config->get(Enum::APP_NAME);

echo AlertBlock::widget([
	'delay'           => 5000,
	'useSessionFlash' => TRUE
]);
?>

<div class="pull-pull-right" style="text-align: left">
	<div class="panel panel-default pull-right" id="login-form" 
            style="max-width: 400px;
            margin: 0 auto 20px;
           
            background-color: #ffffff;
            border: 1px solid black;
            opacity: 0.6;
             ">
		<div class="panel-heading text-center">¡Ingrese al sistema!</div>
		<div class="panel-body">
			<?php $form = ActiveForm::begin([
				'id'                   => 'login-form2',
				'enableAjaxValidation' => FALSE,
                                'validateOnSubmit'=>true,
			]); ?>
                    <div class="text-center">
                            <small>
                            <?php
                                if ($canRegister == 1) {
                                        ?>
                                       
                                        <?= Html::a(Yii::t('app', 'Don\'t have an account?'),
                                                Yii::$app->urlManager->createUrl('//user/registration/register')) ?>
                                 <hr>
                                <?php
                                }
                                ?>
                            </small>
                        </div>
                    
                    <p class="text-center"><?= 'Por favor, rellene los siguiente campos para iniciar sesión:' ?></p>
                        


			<div class="form-group" style="text-align: left">
				<?= $form->field($model, 'username')->textInput(['class' => 'form-control']) ?>
			</div>
			<div class="form-group" style="text-align: left">
				<?= $form->field($model, 'password')->passwordInput(['class' => 'form-control']) ?>
			</div>
                        <hr>
                         <div class="form-group">
				<?php /*
                            $form->field($model, 'captcha')->widget(Recaptcha::className(), [
					'clientOptions' => [
						'data-sitekey' => Yii::$app->config->get(Enum::RECAPTCHA_SITE_KEY)
					]
				]) */?>
			</div>
			
			<hr>
                        <!--div class="checkbox">
				<?php //= $form->field($model, 'rememberMe')->checkbox() ?>
			</div-->
			
				
			


			<hr/>

			<div class="row">
				<div class="col-md-4">
					<?= Html::submitButton(Yii::t('app', 'Sign In'),
                                               ['class' => 'btn btn-large btn-primary', 
                                                  'name'  => 'submit-login',
                                                  'id'    => 'submit-login', ]) ?>
				</div>

				<?php
				if ($canRecoverPassword == 1) {
					?>
					<div class="col-md-8 text-right">
						<small>
							<?= Html::a(Yii::t('app', 'Forgot your password?'),
								Yii::$app->urlManager->createUrl('//user/recovery/recover-password')
							) ?>
						</small>
					</div>
				<?php
				}
				?>
			</div>

			

			<?= AuthChoice::widget(['baseAuthUrl' => ['/user/auth/authenticate']]) ?>

			<?php $form::end(); ?>
		</div>
	</div>
</div>