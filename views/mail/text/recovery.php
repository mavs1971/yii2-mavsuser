<?php
	/**
	 * Created by PhpStorm.
	 * User: Abhimanyu
	 * Date: 10-02-2015
	 * Time: 15:45
	 */

	use yii\helpers\Html;

	/** @var $user \abhimanyu\user\models\User */
?>
Saludos cordiales,

Ud ha solicitado reestablecer su contraseña. Para poder realizar esta orden, requerimos que confirme que
Ud fue quien realizó esta solicitud. Por favor vaya al siguiente enlace para completar su requerimiento.

<?= Html::a('Cambiar contraseña', ['http://tiproduccion.dyndns.org/user/recovery/reset', 'id' => $user->id, 'code' => $user->password_reset_token]) ?>

P.S. Si ud. no realizo esta solicitud y considera que este correo es un error, por favor ignorelo. sus datos no seran cambiados.
