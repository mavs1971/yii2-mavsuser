<?php

	namespace mavs1971\user\models;

	use mavs1971\user\Mailer;
	use Yii;
	use yii\base\Model;
	use yii\web\NotAcceptableHttpException;

	class AccountRecoverPasswordForm extends Model
	{
		public $verifyCode;
		public $email;

		public function rules()
		{
			return [
				// email
				['email', 'required'],
				['email', 'email'],
				['email', 'exist', 'targetClass' => User::className(), 'message' => 'Correo no registrado en el sistema!'],
			];
		}

		public function attributeLabels()
		{
			return [
				'email' => 'Email'
			];
		}

		/**
		 * Sends recovery message.
		 */
		public function recoverPassword()
		{
			$user = User::findOne(['email' => $this->email]);

			if ($user != NULL) {
				$user->password_reset_token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();

				$user->save(FALSE);
			}

			// Sends recovery mail

			try {
				// Send Welcome Message to activate the account
				Mailer::sendRecoveryMessage($user);
			} catch (\Swift_TransportException $e) {
				throw new NotAcceptableHttpException("Ha ocurrido un error en el envio del 
		    				correo para reestablecer  su cuenta. Comuniquese con el administrador del sistema al " 
		    				. Yii::$app->config->get('movil', 'administrador'));
			}
								
			Yii::$app->session->setFlash('info', 'un enlace para reestablecer su password sera enviado a su correo electronico.');
		}
	}