<?php

namespace mavs1971\user\models;

use RuntimeException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use abhimanyu\installer\helpers\Configuration;
use abhimanyu\installer\helpers\enums\Configuration as Enum;
use frontend\models\Datos;
/**
 * User model
 *
 * @property integer $id
 * @property string  $username
 * @property string  $password_hash
 * @property string  $password_reset_token
 * @property string  $email
 * @property string  $auth_key
 * @property string  $activation_token
 * @property integer $super_admin
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string  $password write-only password
 */
class User extends ActiveRecord
{
	/** User Status - Blocked/Inactive */
	const STATUS_BLOCKED = 0;
	/** User Status - Active */
	const STATUS_ACTIVE = 1;
	/** User Status - Activation Pending */
	const STATUS_PENDING = 2;

	/** Login Type - Email and Username */
	const  LOGIN_TYPE_BOTH = 0;
	/** Login Type - Email Only */
	const  LOGIN_TYPE_EMAIL = 1;
	/** Login Type - Username Only */
	const  LOGIN_TYPE_USERNAME = 2;

	/** @var string Plain password. Used for model validation. */
	public $password;
	/** @var string Plain password. Used for model validation. */
	public $password_confirm;
        public $email_confirm;              

        /** se agregar los campor para el registro de socios*/
	public $numero_accion;
	public $cedula;
	public $apellidos;
	public $nombres;
	public $fecha_nacimiento;
        // se agrega campo rif 01/02/2017
        public $rif;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user}}';
	}

	/**
	 * Finds out if password reset token is valid
	 *
	 * @param string $token password reset token
	 *
	 * @return boolean
	 */
	public static function isPasswordResetTokenValid($token)
	{
		if (empty($token)) {
			return FALSE;
		}
		$expire = Yii::$app->params['user.passwordResetTokenExpire'];
		$parts = explode('_', $token);
		$timestamp = (int)end($parts);

		return $timestamp + $expire >= time();
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		$param = Configuration::getParam();
		return [
			'timestamp' => [
				'class'      => TimestampBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
				],
				'value'      => $param['installer']['db']['installer_dbdriver']=='sqlsrv' ? new Expression('GETDATE()') : new Expression('NOW()')
			],
		];
	}

	public function scenarios()
	{
		return [
			'register' => ['username', 'email', 'password', 'password_confirm','numero_accion','cedula','apellidos','nombres','fecha_nacimiento','email_confirm','movil_operadora','movil_numero'],
			'register_a' => ['username', 'email', 'password', 'password_confirm','email_confirm'],
			'recover'  => ['email'],
			'reset'    => ['password', 'password_confirm'],
			'create'   => ['username', 'email', 'password'],
			'update'   => ['username', 'email', 'password'],
                        'juridico' => ['username', 'email', 'password', 'password_confirm','numero_accion','rif','email_confirm','movil_operadora','movil_numero'],
                        'register_simple' => ['username', 'email', 'password', 'password_confirm','numero_accion','cedula','email_confirm','movil_operadora','movil_numero'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [

			// se agregan rules para los campos del registro de socios

		// numero de accion
			['numero_accion', 'required', 'on' => ['register','register_simple']],
			//['numero_accion', 'match', 'pattern' => '/^(([A-Z]|[0-0]){2})([\-]{1})([0-9]{5})([\-]{1})([0-9]{2})+$/', 'message'=>'Formato valido ejemplo AL-00000-99'],
			['numero_accion', 'integer','max' => 99999],
			['numero_accion', 'string', 'max' => 5],
			//['numero_accion', 'filter', 'filter' => 'strtoupper'],

			// cedula
			['cedula', 'required','on' => ['register','register_simple']],
			['cedula', 'integer','max' => 99999999],

			//apellidos
			['apellidos', 'required', 'on' => ['register']],
			//['apellidos', 'match', 'pattern' => '/^([a-zA-Z]\w)|([a-zA-Z]\w[\ ][a-zA-Z]\w)+$/'],
			['apellidos', 'string', 'max' => 80],
			['apellidos', 'trim'],

			//nombres
			['nombres', 'required', 'on' => ['register']],
			//['nombres', 'match', 'pattern' => '/^[a-zA-Z]+$/'],
			['nombres', 'string', 'max' => 80],
			['nombres', 'trim'],

			//fecha_nac
			['fecha_nacimiento', 'safe', 'on' => ['register']],
			//['fecha_nacimiento', 'required', 'on' => ['register']],
			['fecha_nacimiento', 'date'],

                        // rif para juridoco
                        ['rif', 'required','on' => ['juridico']],
                        ['rif', 'safe','on'=>['juridico']],
			['rif', 'string', 'max' => 12],
			['rif', 'trim'],

			// username
			['username', 'required', 'on' => ['register', 'register_a', 'create','register_simple']],
			['username', 'match', 'pattern' => '/^[a-zA-Z0-9_]\w+$/','message'=>'{attribute} no puede contener espacios en blanco ni caracteres especiales como @ ·Ñ *.'],
			['username', 'string', 'min' => 6, 'max' => 25],
			['username', 'unique'],
			['username', 'trim'],

			// email
			['email', 'required', 'on' => ['register', 'register_a', 'create','register_simple']],
			['email', 'email'],
			['email', 'string', 'max' => 255],
			['email', 'unique'],
			['email', 'trim'],

			// password
			['password', 'required', 'on' => ['register', 'register_a', 'reset', 'create','register_simple']],
			['password', 'string', 'min' => 6, 'on' => ['register', 'reset', 'create', 'update','register_simple']],

			// password confirm
			['password_confirm', 'required', 'on' => ['register', 'register_a', 'reset','register_simple']],
			['password_confirm', 'compare', 'compareAttribute' => 'password'],
                    
                        // onfirmar correo
			['email_confirm', 'required', 'on' => ['register', 'register_a','register_simple']],
			['email_confirm', 'compare', 'compareAttribute' => 'email'],
                    
                    // /// telefono mil agregado el 16/08/2019 para comenzar a tener numero movil registrado
			['movil_operadora', 'required', 'on' => ['register', 'register_a', 'create','register_simple']],
			['movil_operadora', 'match', 'pattern' => '/^[0-9_]\w+$/'],
			['movil_operadora', 'string', 'min' => 4, 'max' => 4],
			//['movil_operadora', 'unique'],
			['movil_operadora', 'trim'],
                     // /// telefono mil agregado el 16/08/2019 para comenzar a tener numero movil registrado
			['movil_numero', 'required', 'on' => ['register', 'register_a', 'create','register_simple']],
			['movil_numero', 'match', 'pattern' => '/^[0-9_]\w+$/'],
			['movil_numero', 'string', 'min' => 7, 'max' => 7],
			//['movil_numero', 'unique'],
			['movil_numero', 'trim'],
		];
	}

	public function attributeLabels()
	{
		return [
			'username'         => yii::t('app','Username'),
			'password_hash'    => yii::t('app','Password'),
			'password_confirm' => yii::t('app','Confirm Password'),
                        'email_confirm' => yii::t('app','Confirm email'),
			'email'            => yii::t('app','Email'),
			'numero_accion' 	=> 'Número de Acción',
			'cedula'        	=> 'C.I. Socio',
			'apellidos' 		=> 'Apellidos',
			'nombres'  			=> 'Nombres',
			'fecha_nacimiento'	=> 'Fecha de Nacimiento',
                        'password'    => yii::t('app','Password'),
                        'movil_operadora'=>'operadora',
                        'movil_numero'=>'numero'
		];
	}

	/**
	 * Validates password
	 *
	 * @param string $password password to validate
	 *
	 * @return boolean if password provided is valid for current user
	 */
	public function validatePassword($password)
	{
		return Yii::$app->security->validatePassword($password, $this->password_hash);
	}

	public function beforeSave($insert)
	{
		if ($insert) {
			$this->generateAuthKey();
			$this->generateActivationToken();
		}

		if ($this->password)
			$this->setPassword($this->password);

		return parent::beforeSave($insert);
	}

	/**
	 * Generates "remember me" authentication key
	 */
	public function generateAuthKey()
	{
		$this->auth_key = Yii::$app->security->generateRandomString();
	}

	/**
	 * Generates account activation token
	 */
	public function generateActivationToken()
	{
		$this->activation_token = Yii::$app->security->generateRandomString(24);
	}

	/**
	 * Generates password hash from password and sets it to the model
	 *
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password_hash = Yii::$app->security->generatePasswordHash($password);
	}

	public function afterSave($insert, $changedAttributes)
	{
		if ($insert) {
			$profile = Yii::createObject([
				'class' => Profile::className(),
				'uid'   => $this->id
			]);
			$profile->save(FALSE);
		}

		parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * Gets user profile
	 *
	 * @return Profile
	 */
	public function getProfile()
	{
		return $this->hasOne(Profile::className(), ['uid' => 'id']);
	}

	/**
	 * This method is used to register new user account.
	 *
	 * @param bool $isSuperAdmin
	 * @param int  $status
	 *
	 * @return bool
	 */
	public function register($isSuperAdmin = FALSE, $status = 1)
	{
		if ($this->getIsNewRecord() == FALSE) {
			throw new RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
		}

		// Set to 1 if isSuperAdmin is true else set to 0
		$this->super_admin = $isSuperAdmin ? 1 : 0;

		// Set status
		$this->status = $status;
		$this->fecha_nacimiento ='';

		// Save user data to the database
		if ($this->save()) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @return bool Whether the user is an admin or not.
	 */
	public function getIsAdmin()
	{
		return $this->super_admin ;
	}

	public function create($isSuperAdmin = FALSE)
	{
		if ($this->getIsNewRecord() == FALSE) {
			throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
		}

		// Set to 1 if isSuperAdmin is true else set to 0
		$this->super_admin = $isSuperAdmin ? 1 : 0;

		// Set status
		$this->status = User::STATUS_PENDING;

		// Save user data to the database
		if ($this->save()) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Returns user's status
	 *
	 * @return null|string
	 */
	public function getIsStatus()
	{
		switch ($this->status) {
			case User::STATUS_PENDING:
				return '<div class="text-center"><span class="text-primary">Pending</span></div>';
			case User::STATUS_ACTIVE:
				return '<div class="text-center"><span class="text-success">Active</span></div>';
			case User::STATUS_BLOCKED:
				return '<div class="text-center"><span class="text-danger">Blocked</span></div>';
		}

		return NULL;
	}

	/**
	 * Returns TRUE if user is confirmed else FALSE
	 *
	 * @return bool
	 */
	public function getIsConfirmed()
	{
		return $this->status != User::STATUS_PENDING;
	}

	/**
	 * Returns TRUE if user is blocked else FALSE
	 *
	 * @return bool
	 */
	public function getIsBlocked()
	{
		return $this->status == User::STATUS_BLOCKED;
	}

	/**
	 * Confirms user and sets status to ACTIVE
	 */
	public function confirm()
	{
		$this->status = User::STATUS_ACTIVE;
		if ($this->save(FALSE))
			return TRUE;

		return FALSE;
	}

	/**
	 * Blocks the user and sets the status to BLOCKED
	 */
	public function block()
	{
		$this->status = User::STATUS_BLOCKED;

		if ($this->save(FALSE))
			return TRUE;

		return FALSE;
	}

	/**
	 * Unblocks the user and sets the status to ACTIVE
	 */
	public function unblock()
	{
		$this->status = User::STATUS_ACTIVE;

		if ($this->save(FALSE))
			return TRUE;

		return FALSE;
	}

	public function getDatos()
    {
        return $this->hasOne(Datos::className(), ['login_id' => 'id']);
    }

}