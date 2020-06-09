<?php
/**
 * Created by PhpStorm.
 * User: Abhimanyu
 * Date: 09-02-2015
 * Time: 18:14
 */

namespace mavs1971\user\controllers;

use mavs1971\user\models\AccountRecoverPasswordForm;
use mavs1971\user\models\User;
use mavs1971\user\UserModule;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RecoveryController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow'         => TRUE,
						'actions'       => ['recover-password', 'reset'],
						'roles'         => ['?'],
						'matchCallback' => function ($rule, $action) {
							if (UserModule::$canRecoverPassword)
								return TRUE;

							return FALSE;
						}
					]
				]
			]
		];
	}

	/**
	 * Sends password recovery mail to the user
	 *
	 * @return string
	 */
	public function actionRecoverPassword()
	{
		$model = new AccountRecoverPasswordForm();

		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate()) {
				$model->recoverPassword();
			}
		}

		return $this->render('recoverPassword', ['model' => $model]);
	}

	/**
	 * @param integer $id   User Id
	 * @param string  $code Password Reset Token
	 *
	 * @return string
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionReset($id, $code)
	{
		$model = User::findOne([
			'id'                   => $id,
			'password_reset_token' => $code
		]);

		if ($model == NULL)
			throw new \yii\web\NotAcceptableHttpException("El enlace para reestablecer su clave del sistema de autogestion ha vencido y/o"
                                . " ya no esa disponible, solicite una nuevo correo desde la opcion de 'Olvido su clave'");
                
                if ($model->status !== User::STATUS_ACTIVE)
                    throw new \yii\web\NotAcceptableHttpException("Su Cuenta no esta activa, si acaba de crear su usuario en el sistema de autogetion recuerde activar la misma desde el enlace enviado a su correo"
                            . "enviado su correo.");

		$model->scenario = 'reset';

		if (!empty($model)) {
			if ($model->load(Yii::$app->request->post())) {
				if ($model->validate()) {
					$model->password_reset_token = NULL;
					$model->save();

					Yii::$app->session->setFlash('success', Yii::t('user', 'Your password has successfully been changed. Now you can login with your new password.'));

					return $this->redirect(['//user/auth/login']);
				}
			}
		}

		return $this->render('reset', ['model' => $model]);
	}
}