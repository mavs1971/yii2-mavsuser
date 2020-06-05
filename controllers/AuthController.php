<?php

namespace mavs1971v\user\controllers;

use mavs1971\user\models\AccountLoginForm;
use mavs1971\user\models\SocialAccount;
use frontend\models\AUDITORWEB;
use mavs1971\user\UserModule;
use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\db\Expression;

class AuthController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow'   => TRUE,
						'actions' => ['login', 'auth','login2'],
						'roles'   => ['?']
					],
					[
						'allow'   => TRUE,
						'actions' => ['logout'],
						'roles'   => ['@']
					]
				]
			],
			'verbs'  => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'logout' => ['post']
				]
			]
		];
	}

	public function actions()
	{
		return [
			'auth' => [
				'class'           => AuthAction::className(),
				'successCallback' => [$this, 'authenticate'],
			]
		];
	}

	public function authenticate(ClientInterface $client)
	{
		$attributes = $client->getUserAttributes();
		$provider = $client->getId();
		$clientId = $attributes['id'];

		$model = SocialAccount::find()->where(['provider' => $provider, 'client_id' => $clientId])->one();

		if ($model === NULL) {
			$model->save(FALSE);
		}

		if (NULL === ($user = $model->getUser())) {
			$this->action->successUrl = Url::to(['/user/registration/connect', 'account_id' => $model->id]);
		} else {
			Yii::$app->user->login($user, UserModule::$rememberMeDuration);
		}
	}

	/**
	 * Displays the login page.
	 *
	 * @return string|\yii\web\Response
	 */
	public function actionLogin()
	{
		// If the user is logged in, redirect to dashboard
		if (!Yii::$app->user->isGuest)
			return $this->redirect(Yii::$app->user->returnUrl);

		$model = new AccountLoginForm();
                $model->scenario='socio';

		if ($model->load(Yii::$app->request->post())  && $model->login()){
                       $this->grabaAuditoria('LW');
                       $DATOSSOCIO = \frontend\models\Datos::findOne(['login_id' => Yii::$app->user->getId()]);
                       if ($DATOSSOCIO->ccgrupo['GR_NO_PERMITIR_COBRO']){
                           $model->addError('password', 'NO TIENE PERMITIDO ACCEDER AL SITIO. PASAR POR ADMINISTRACION.');     
                            Yii::$app->user->logout(true);
                            return $this->render('login', [
                                    'model'              => $model,
                                    'canRegister'        => UserModule::$canRegister,
                                    'canRecoverPassword' => UserModule::$canRecoverPassword
                                    //'queloginerror'		 => $model->login()
                            ]);
                       }else {
                            return $this->redirect(Yii::$app->user->returnUrl);
                       }
                }
		return $this->render('login', [
			'model'              => $model,
			'canRegister'        => UserModule::$canRegister,
			'canRecoverPassword' => UserModule::$canRecoverPassword
			//'queloginerror'		 => $model->login()
		]);
	}

        /**
	 * Displays the login page.
	 *
	 * @return string|\yii\web\Response
	 */
	public function actionLogin2()
	{
		// If the user is logged in, redirect to dashboard
		if (!Yii::$app->user->isGuest)
			return $this->redirect(Yii::$app->user->returnUrl);

		$model = new AccountLoginForm();
                $model->scenario='admin';

		if ($model->load(Yii::$app->request->post())  && $model->login()){
                       $this->grabaAuditoria('LW');
                        return $this->redirect(Yii::$app->user->returnUrl);
                }
		return $this->render('login2', [
			'model'              => $model,
			'canRegister'        => UserModule::$canRegister,
			'canRecoverPassword' => UserModule::$canRecoverPassword
			//'queloginerror'		 => $model->login()
		]);
	}
	/**
	 * Logs the user out and then redirects to the homepage.
	 *
	 * @return \yii\web\Response
	 */
	public function actionLogout()
	{        
                $this->grabaAuditoria('CW');        
                Yii::$app->user->logout();
		return $this->goHome();
	}
        
        private function grabaAuditoria($tipo){
            $auditoria = new AUDITORWEB();
            // procedemos a generar registro en 
            $expression = new Expression('CONVERT (date, GETDATE())');
            $expression2 = new Expression('CONVERT (time, GETDATE())');
            $Fecha = (new \yii\db\Query)->select($expression)->scalar();  // SELECT NOW();   
            $Hora = (new \yii\db\Query)->select($expression2)->scalar();  // SELECT NOW();    
            $auditoria->AUFECHA = $Fecha;
            $auditoria->AUHORA = $Hora;
            $auditoria->AUUSUARIO = Yii::$app->user->identity->username;
            $auditoria->AUTABLA = 'MODULOWEB';
            $auditoria->AUTIPO=$tipo;
            $auditoria->AUCODIGO = Yii::$app->request->userIP; 
            $auditoria->AUBARRA='WEB';
            $auditoria->save(false);
        }
}