<?php
/**
 * Created by PhpStorm.
 * User: Abhimanyu
 * Date: 09-02-2015
 * Time: 17:54
 */

namespace mavs1971\user\controllers;

use mavs1971\user\Mailer;
use mavs1971\user\models\User;
use mavs1971\user\models\UserIdentity;
use mavs1971\user\UserModule;
//  enlace tabla de socios
use frontend\models\Datos;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Controller that manages user registration process.
 */
class RegistrationController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow'         => TRUE,
						'actions'       => ['register', 'confirm','juridico','register_sencillo','register_full'],
						'roles'         => ['?'],
						'matchCallback' => function ($rule, $action) {
							if (UserModule::$canRegister)
								return TRUE;

							return FALSE;
						}
					]
				]
			],
		];
	}

	/**
	 *  Register the user
	 *
	 * @return string|\yii\web\Response
	 */
	/**
	 *  Register the user
	 *
	 * @return string|\yii\web\Response
	 */
	public function actionRegister()
	{
            if (Yii::$app->config->get('registro')=='0'){
                $this->redirect([Yii::$app->urlManager->createUrl('/user/registration/register_full')]);
            }else{
                $this->redirect([Yii::$app->urlManager->createUrl('/user/registration/register_sencillo')]);
            }
            
	}

        
        /* rutina de registro con full verificacion
         * 
         */
        
        public function actionRegister_full()
	{
		$model = new user();
		$model->scenario = 'register';

		if ($model->load(Yii::$app->request->post())) {
			//comienza la consulta de socios y verificacion de datos

			// quitamos guiones del campo numero de accion
			$num_accion = $model->numero_accion; 
			//AGREGAMOS CEROS A LA DERECHA DEL VALOS DE LA CEDULA
			$cedula = 	substr('0000' . $model->cedula, strlen('0000' . $model->cedula)-9);
			// buscamos los datos en la tabla de socios DATOS
			$socios = Datos::find()
				->where(['CEDULA' => $cedula,'ACCIONP' => $num_accion])
				->one();
			//quitamos espacios en blanco de cada campo
				$nombresolo = preg_replace('/\s/', '', $socios->NOMBRES);
				$apellidosolo = preg_replace('/\s/', '', $socios->APELLIDOS);
			// preguntamos por combinacion accion+cedula
			if (!$socios) { 				

				Yii::$app->session->setFlash(
					'danger', 'Combinacion de Cedula + No. de accion no registrada'  );

 			} elseif (trim($apellidosolo) == trim($model->apellidos))  {
		
				if (trim($nombresolo) == trim($model->nombres))  {
			
					
					if ($model->fecha_nacimiento ==  $socios->Fecha_NacText) {

						 //$model->fecha_nacimiento =$socios->FECHANACIMIENTO1;
 						if ($socios->login_id) {

							Yii::$app->session->setFlash(
								'danger', 'Ya se ha registrado un usuario web para esta acción, utilice la opción OLVIDO SU CLAVE para password, comuníquese con el Telefono: ' . 
							Yii::$app->config->get('movil', 'administrador'));

						} elseif ((Yii::$app->config->get('parametro1')=='0') and (!$socios->Estitular)) {
                                                    Yii::$app->session->setFlash(
                                                                'danger', 'El sistema solo permite registrar a Socios Titulares... hable con administracion si cree Ud. que esto es un error'  );
                                                } else {
							if ( $model->register(FALSE, User::STATUS_PENDING) ) {

								$socios->login_id = $model->id;
								$socios->save();
                                                                // agregamos opcion de enviar correo personalizado
                                                                $opcion = \frontend\models\WBMENU::findOne(['ID'=>Yii::$app->config->get('Bienvenida')]);
                                                                if ($opcion->STATUS){
                                                                    $opcion->CORREO = $model->email;
                                                                    $newbody= Yii::$app->correohelper->Preparacorreo($socios,$opcion,$model);
                                                                    $opcion->CUERPO=$newbody;
                                                                    try {
                                                                    // Send Welcome Message to activate the account
                                                                        Mailer::sendbasecorreo($opcion,$opcion->CORREO);
                                                                    } catch (\Swift_TransportException $e) {
                                                                         throw new NotFoundHttpException("Ha ocurrido un error en el envio del 
                                                                         de validacion. verifique la redaccion del mismo y su contenido");
                                                                    }

                                                                } else {
                                                                 try {
                                                                         // Send Welcome Message to activate the account
                                                                         Mailer::sendWelcomeMessage($model);
                                                                 } catch (\Swift_TransportException $e) {
                                                                     throw new NotFoundHttpException("Ha ocurrido un error en el envio del 
                                                                                                 correo para certificar la activacion de su cuenta. Comuniquese con el administrador del sistema al " 
                                                                                                 . Yii::$app->config->get('movil', 'administrador'));
                                                                 }
                                                                }

								Yii::$app->session->setFlash(
									'danger', Yii::t('app', 'You\'ve successfully been registered. Check your mail to activate your account'));

								return $this->redirect(Yii::$app->urlManager->createUrl('//user/auth/login'));
							}	else {

								Yii::$app->session->setFlash(
									'danger', 'LLAME AL ADMINISTRADOR, PROBLEMAS DE ACCESO '  . 
									Yii::$app->config->get('movil'));
                                                                
							}
							

						}		

					} else {

					Yii::$app->session->setFlash(
						'danger', 'Su fecha de nacimiento no coinciden con su registro de datos, comuníquese con el Telefono: ' . 
					Yii::$app->config->get('movil'));
					}

				} else {

					Yii::$app->session->setFlash(
						'danger', 'Nombres no coinciden con su registro de datos, comuníquese con el Telefono: ' . 
					Yii::$app->config->get('movil'));
				}
			} else {

				Yii::$app->session->setFlash(
					'danger', 'Apellidos no coinciden con su registro de datos, comuníquese con el Telefono: ' . 
					Yii::$app->config->get('movil'));


			}

		}

		return $this->render('register', ['model' => $model]);
	}

        /*
         * registro sencillo
         * 
         * 
         */
        
        public function actionRegister_sencillo()
	{
		$model = new user();
		$model->scenario = 'register_simple';

		if ($model->load(Yii::$app->request->post())) {
			//comienza la consulta de socios y verificacion de datos

			// quitamos guiones del campo numero de accion
			$num_accion = $model->numero_accion; 
			//AGREGAMOS CEROS A LA DERECHA DEL VALOS DE LA CEDULA
			$cedula = 	substr('0000' . $model->cedula, strlen('0000' . $model->cedula)-9);
			// buscamos los datos en la tabla de socios DATOS
			$socios = Datos::find()
				->where(['CEDULA' => $cedula,'ACCIONP' => $num_accion])
				->one();
			// preguntamos por combinacion accion+cedula
			if (!$socios) { 				

				Yii::$app->session->setFlash(
					'danger', 'Combinacion de Cedula + No. de accion no registrada'  );

 			} elseif ($socios->login_id) {

                            Yii::$app->session->setFlash(
                                    'danger', 'Ya se ha registrado un usuario web para esta acción, utilice la opción OLVIDO SU CLAVE para password, comuníquese con el Telefono: ' . 
                            Yii::$app->config->get('movil', 'administrador'));

                        } elseif ((Yii::$app->config->get('parametro1')=='0') and (!$socios->Estitular)) {
                            Yii::$app->session->setFlash(
                                        'danger', 'El sistema solo permite registrar a Socios Titulares... hable con administracion si cree Ud. que esto es un error'  );
                        } elseif ( $model->register(FALSE, User::STATUS_PENDING) ) {

                                $socios->login_id = $model->id;
                                $socios->save();
                                $opcion = \frontend\models\WBMENU::findOne(['ID'=>Yii::$app->config->get('Bienvenida')]);
                                if ($opcion->STATUS){
                                    $opcion->CORREO = $model->email;
                                    $newbody= Yii::$app->correohelper->Preparacorreo($socios,$opcion,$model);
                                    $opcion->CUERPO=$newbody;
                                    try {
                                    // Send Welcome Message to activate the account
                                        Mailer::sendbasecorreo($opcion,$opcion->CORREO);
                                    } catch (\Swift_TransportException $e) {
                                         throw new NotFoundHttpException("Ha ocurrido un error en el envio del 
                                         de validacion. verifique la redaccion del mismo y su contenido");
                                    }

                                } else {
                                    try {
                                            // Send Welcome Message to activate the account
                                            Mailer::sendWelcomeMessage($model);
                                    } catch (\Swift_TransportException $e) {
                                        throw new NotFoundHttpException("Ha ocurrido un error en el envio del 
                                                                    correo para certificar la activacion de su cuenta. Comuniquese con el administrador del sistema al " 
                                                                    . Yii::$app->config->get('movil', 'administrador'));
                                    }
                                }


                                Yii::$app->session->setFlash(
                                        'danger', Yii::t('app', 'You\'ve successfully been registered. Check your mail to activate your account'));

                                return $this->redirect(Yii::$app->urlManager->createUrl('//user/auth/login'));
                        }else {

                                Yii::$app->session->setFlash(
                                        'danger', 'Ocurrio un problema al momento del regisro, LLAME AL ADMINISTRADOR, PROBLEMAS DE ACCESO '  . 
                                        Yii::$app->config->get('movil'));

                        }
                                		

		}

		return $this->render('register_sencillo', ['model' => $model]);
	}

        
	/**
	 * Confirms user's account.
	 *
	 * @param integer $id   User Id
	 * @param string  $code Activation Token
	 *
	 * @return string
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionConfirm($id, $code)
	{
		$user = UserIdentity::findByActivationToken($id, $code);

		if ($user == NULL)
			throw new NotFoundHttpException;

		if (!empty($user)) {
			$user->activation_token = NULL;
			$user->status = User::STATUS_ACTIVE;
			$user->save(FALSE);

			Yii::$app->session->setFlash('success', Yii::t('user', 'Felicitaciones! Su cuenta ' . $user->email . ' ha sido activada con exito'));
		} else
			Yii::$app->session->setFlash('error', Yii::t('user', 'La cuenta ' . $user->email . ' No ha podido ser activada. contacte con el administrador'));

		return $this->render('confirm', ['user' => $user]);
	}

        
       public function actionJuridico()
	{
		$model = new user();
		$model->scenario = 'juridico';

		if ($model->load(Yii::$app->request->post())) {
			//comienza la consulta de socios y verificacion de datos

			// quitamos guiones del campo numero de accion
			$num_accion = $model->numero_accion; 
			//AGREGAMOS CEROS A LA DERECHA DEL VALOS DE LA CEDULA
			$rif = $model->rif;
			// buscamos los datos en la tabla de socios DATOS
			$socios = Datos::find()
				->where(['RIF' => $rif,'ACCIONP' => $num_accion])
				->one();
			// preguntamos por combinacion accion+cedula
			if (!$socios) { 				

				Yii::$app->session->setFlash(
					'danger', 'Combinacion de RIF + No. de accion no registrada'  );

 			} elseif ($socios->login_id) {

                                        Yii::$app->session->setFlash(
                                                'danger', 'Ya se ha registrado un usuario web para esta acción, utilice la opción OLVIDO SU CLAVE para password, comuníquese con el Telefono: ' . 
                                        Yii::$app->config->get('movil', 'administrador'));

                        } elseif ((Yii::$app->config->get('parametro1')=='0') and (!$socios->Estitular)) {
                                    Yii::$app->session->setFlash(
                                                    'danger', 'El sistema solo permite registrar a Socios Titulares... hable con administracion si cree Ud. que esto es un error'  );
                        } else {
                                if ( $model->register(FALSE, User::STATUS_PENDING) ) {

                                        $socios->login_id = $model->id;
                                        $socios->save();

                                        try {
                                                // Send Welcome Message to activate the account
                                                Mailer::sendWelcomeMessage($model);
                                        } catch (\Swift_TransportException $e) {
                                            throw new NotFoundHttpException("Ha ocurrido un error en el envio del 
                                                                        correo para certificar la activacion de su cuenta. Comuniquese con el administrador del sistema al " 
                                                                        . Yii::$app->config->get('movil', 'administrador'));
                                        }


                                        Yii::$app->session->setFlash(
                                                'success', Yii::t('app', 'You\'ve successfully been registered. Check your mail to activate your account'));

                                        return $this->redirect(Yii::$app->urlManager->createUrl('//user/auth/login'));
                                }	else {

                                        Yii::$app->session->setFlash(
                                                'danger', 'LLAME AL ADMINISTRADOR, PROBLEMAS DE ACCESO '  . 
                                                Yii::$app->config->get('movil'));

                                } 
                        }
                }
		return $this->render('juridico', ['model' => $model]);
	}


}