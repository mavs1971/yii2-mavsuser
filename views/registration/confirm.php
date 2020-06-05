<?php
	/**
	 * Created by PhpStorm.
	 * User: Abhimanyu
	 * Date: 10-02-2015
	 * Time: 22:23
	 */

	use kartik\alert\AlertBlock;

	/** @var $this   yii\web\View */
	/** @var $user \abhimanyu\user\models\User */

	$this->title = 'Activación de Cuenta - ' . Yii::$app->name;

	echo AlertBlock::widget([
		                        'delay'           => 5000,
		                        'useSessionFlash' => TRUE
	                        ]);
?>