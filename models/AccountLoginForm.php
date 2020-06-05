<?php

namespace mavs1971\user\models;

use abhimanyu\installer\helpers\enums\Configuration as Enum;
use mavs1971\user\UserModule;
use Yii;
use yii\base\Model;
//use Zelenin\yii\widgets\Recaptcha\validators\RecaptchaValidator;

/**
 * LoginForm is the model behind the login form.
 *
 */
class AccountLoginForm extends Model
{
	public  $username;
	public  $password;
	public  $rememberMe = TRUE;
	private $_user      = FALSE;
	public  $captcha;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			// username and password are both required
			[['username', 'password'], 'required'],
			// rememberMe must be a boolean value
			['rememberMe', 'boolean'],
			// password is validated by validatePassword()
			['password', 'validatePassword'],
			// captcha
                        // [['captcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => Yii::$app->config->get(Enum::RECAPTCHA_SECRET), 'uncheckedMessage' => 'Por vavor confirme que no es una maquina.']
			//['captcha', RecaptchaValidator::className(), 'secret' => Yii::$app->config->get(Enum::RECAPTCHA_SECRET)],
                        ['captcha', 'safe'],
		];
	}
        
        public function scenarios()
        {
            return [
                'admin'  => [
                    'username',
                    'password',
                   
                ],
                'socio'  => [
                    'username',
                    'password',
                    'captcha'
                ],
            ];
        }

	/**
	 * Validates the password.
	 * This method serves as the inline validation for password.
	 *
	 * @param string $attribute the attribute currently being validated
	 * @param array  $params    the additional name-value pairs given in the rule
	 */
	public function validatePassword($attribute, $params)
	{
		if (!$this->hasErrors()) {
			$user = $this->getUser();
			if (!$user){
                            $this->addError($attribute, 'usuario no valido.');
                        }elseif (!$user->validatePassword($this->password)) {
				$this->addError($attribute, 'Verifique sus credenciales de acceso.');
                        }
			
		}
	}

	/**
	 * Finds user by [[username]]
	 *
	 * @return User|null
	 */
	public function getUser()
	{
		if ($this->_user === FALSE) {
			if (UserModule::$loginType == User::LOGIN_TYPE_EMAIL) {
				$this->_user = UserIdentity::findByEmail($this->username);
			} elseif (UserModule::$loginType == User::LOGIN_TYPE_USERNAME) {
				$this->_user = UserIdentity::findByUsername($this->username);
			} elseif (UserModule::$loginType == User::LOGIN_TYPE_BOTH) {
				$this->_user = UserIdentity::findByEmail($this->username);
                if ($this->_user) {} else {$this->_user = UserIdentity::findByUsername($this->username);}
			}
		}

		return $this->_user;
	}

	/**
	 * Logs in a user using the provided username and password.
	 *
	 * @return boolean whether the user is logged in successfully
	 */
	public function login()
	{
		return $this->validate() ? Yii::$app->user->login($this->getUser(), $this->rememberMe ? UserModule::$rememberMeDuration : 0) : FALSE;
		//return Yii::$app->user->login($this->getUser());

	}

	public function attributeLabels()
	{
		return [
			'username'   => UserModule::$loginType == User::LOGIN_TYPE_BOTH ?
			'Email/Username' : (UserModule::$loginType == User::LOGIN_TYPE_EMAIL ? Yii::t('app', 'Email') : Yii::t('app', 'Username')),
			'password'   => Yii::t('app', 'Password'),
			'rememberMe' => Yii::t('app', 'Remember Me?'),
                        'captcha'=>'Verificación acceso',
		];
	}
}