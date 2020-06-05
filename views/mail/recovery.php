<?php
	/**
	 * Created by PhpStorm.
	 * User: mavs1971
	 * Date: 09-02-2015
	 * Time: 19:04
	 */

	use yii\helpers\Html;

	/** @var $user \abhimanyu\user\models\User */
?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	Saludos Cordiales,
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	Ud ha solicitado reiniciar su contraseña, para poder completar esta orden necesitamos verificar que ha sido ud quien inicio esta solicitud. Por favor haga click en el enlace siguiente para proceder a restablecer su contraseña 
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	<?= Yii::$app->config->get('dominio') . '/user/recovery/reset?id=' . $user->id . '&code=' . $user->password_reset_token; ?>
        si no puede hacer click, copie y pegue el enlace en su navegador
</p>
<p>
    Le recordamos sus usuario de afiliacion : <?= $user->username;?>
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	P.D. Si ud no ha hecho esta solicitud, por favor haga caso omiso a este correo y su cuenta quedara sin cambios.
</p>
