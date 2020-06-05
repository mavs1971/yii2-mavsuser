<?php
	/**
	 * Created by PhpStorm.
	 * User: mavs1971
	 * Date: 10-02-2015
	 * Time: 18:31
	 */

	use yii\helpers\Html;

	/** @var $user \abhimanyu\user\models\User */
?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	Saludos Cordiales!,<br/>
	
	Por favor haga Click en el siguiente enlace para proceder a activar su Cuenta:<br/>
	<?= Yii::$app->config->get('dominio') . '/user/registration/confirm?id=' . $user->id . '&code=' . $user->activation_token; ?>
        Si no puede hacer click, copie y pegue el enlace en su navegador web
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	Su cuenta para <?= Yii::$app->name ?> ha sido creada exitosamente. Ud debe utilizar su Usuario y contraseña para acceder.
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	Correo Electronico: <?= $user->email ?><br>
	Usuario: <?= $user->username ?><br>
	Contraseña: la creada por ud, se le recomienda luego de activar su cuenta solicitar cambio de contraseña
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	P.D. Si cree haber recibido este correo por error, elimine este correo de su bandeja.
</p>