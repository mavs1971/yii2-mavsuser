<?php
	/**
	 * Created by PhpStorm.
	 * User: Abhimanyu
	 * Date: 10-02-2015
	 * Time: 18:40
	 */

	use yii\helpers\Html;

	/** @var $user \abhimanyu\user\models\User */
?>
Bienvenido,<br/>
solo hace falta que vaya al enlace siguiente para activar su cuenta:<br/>
<?= Html::a('Activar su cuenta', ['//tiproduccion.dyndns.org/user/registration/confirm', 'id' => $user->id, 'code' => $user->activation_token],
            ['class' => 'btn btn-lg btn-success']) ?>
Su cuenta en <?= Yii::$app->name ?> ha sido creada exitosamente. Los siguientes datos debe utilizar para ingresar al sistema.<br/>

Dirección de correo	: <?= $user->email ?><br>
Usuario				: <?= $user->username ?><br>
contraseña			: la creada por ud, se le recomienda luego de activar su cuenta solicitar cambio de contraseña<br/>

P.D. Si cree haber recibido este error por correo, descarte la información.
     Si no puede hacer click al enlace copie y pegue en su navegador