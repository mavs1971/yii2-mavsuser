<?php
	/**
	 * Created by PhpStorm.
	 * User: Abhimanyu
	 * Date: 10-02-2015
	 * Time: 17:08
	 */

	namespace abhimanyu\user;

	use mavs1971\user\models\User;
	use Yii;
	use yii\base\Component;
	//use yii\base\ViewRenderer;
	use abhimanyu\installer\helpers\enums\Configuration as Enum;
	use abhimanyu\installer\helpers\Configuration;

	class Mailer extends Component
	{
		/**
		 * Sends welcome mail to the user upon registration
		 *
		 * @param \abhimanyu\user\models\User $user
		 *
		 * @return bool
		 */
		public static function sendWelcomeMessage(User $user)
		{
			return Mailer::sendMail($user->email, 'Bienvenido a ' . Yii::$app->name, 'welcome', ['user' => $user]);
		}

		/**
		 * Sends mail using the Swift Mailer
		 *
		 * @param string       $to      Senders Email
		 * @param string       $subject Message Subject
		 * @param string|array $view    Message View
		 * @param array        $params  Message Parameters
		 *
		 * @return bool
		 */
		protected function sendMail($to, $subject, $view, $params = [])
		{
			$parametros = Configuration::getParam();

			if ($parametros['useTransport']==true) {
				$mailer           = Yii::$app->mail;
			} else {
				$mailer           = Yii::$app->mailer;
			}
                        
                        //$mailer           = Yii::$app->mailer;
			$mailer->viewPath = '@abhimanyu/user/views/mail';

			return $mailer->compose(['html' => $view, 'text' => 'text/' . $view], $params)
				->setTo($to)
				->setFrom(Yii::$app->config->get('mail.username'), 'no@reply.com')
				->setSubject($subject)
				->send();
		}

		/**
		 * Sends password recovery mail to the user
		 *
		 * @param \abhimanyu\user\models\User $user
		 *
		 * @return bool
		 */
		public static function sendRecoveryMessage(User $user)
		{
			return Mailer::sendMail($user->email, 'Recuperar Contraseña', 'recovery', ['user' => $user]);
		}

		// se prepara para enviar correo de invitacion
		public static function sendPaseInvitacion($invitado,$direccion)
		{
			//buscamos direccion correos en datos
			return Mailer::sendinvitacion($direccion, 'Registro de invitación ' . $invitado->codigo, 'invitacion_e', ['invitado' => $invitado]);
		}		

		// envia correo con la invitacion electronica al socio
		protected function sendinvitacion($to, $subject, $view, $params = [])
		{
			$parametros = Configuration::getParam();

			if ($parametros['useTransport']==true) {
				$mailer           = Yii::$app->mail;
			} else {
				$mailer           = Yii::$app->mailer;
			}

			$mailer->viewPath = '@app/views/socios/correos';

			return $mailer->compose(['html' => $view, 'text' =>  $view], $params)
				->setTo($to)
				->setFrom(Yii::$app->config->get('mail.username'), 'no@reply.com')
				->setSubject($subject)
				->send();
		}

		// se prepara para agendar correo de invitacion
		public static function agendaPaseInvitacion($invitado,$direccion)
		{
			//buscamos direccion correos en datos
			return Mailer::agendainvitacion($direccion, 'Registro de invitación ' . $invitado->codigo, 'invitacion_e', ['invitado' => $invitado]);
		}


		public static function agendainvitacion($to, $subject, $view, $params = [])
		{
			$parametros = Configuration::getParam();

			if ($parametros['useTransport']==true) {
				$mailer           = Yii::$app->mail;
			} else {
				$mailer           = Yii::$app->mailer;
			}

			$mailer->viewPath = '@app/views/socios/correos';

			$msg = $mailer->compose(['html' => $view, 'text' =>  $view], $params)
				->setTo($to)
				->setFrom(Yii::$app->config->get('mail.username'), 'no@reply.com')
				->setSubject($subject);

			//$authhost="{imap.gmail.com:993/imap/ssl}[Gmail]/Drafts"; 
			$authhost="{imap.gmail.com:993/imap/ssl}";

			$user=Yii::$app->config->get('mail.username'); 
			$pass=Yii::$app->config->get('mail.password'); 

			$conexionIMAP = imap_open ($authhost, $user, $pass) or die("No se puede acceder al buzon de gmail" . imap_last_error());
			$envelope["from"]  = Yii::$app->config->get('mail.username');
			$envelope["to"]  = $to;
			$envelope["subject"]  = $subject;

			$part["type"] = TYPETEXT;
			$part["subtype"] = "plain";
			$part["description"] = "Agenda de Invitación";
			$part["contents.data"] = Yii::$app->view->renderFile('@app/views/socios/correos/text/invitacion_e.php',$params);

			$body[1] = $part;

			$msg2 = imap_mail_compose($envelope, $body);
			$boxes = imap_list($conexionIMAP, $authhost, '*');
		    if (imap_append($conexionIMAP,$boxes[4],$msg2)==false){
		    	//$boxes = imap_list($conexionIMAP, $authhost, '*');
		       	die( "No se pudo Agendar Mensaje : " . imap_last_error() . '<br/>' . print_r($boxes));
		    } else{
				imap_close($conexionIMAP);
		    } 		

			 return $msg->send();
		}

		// envia correo de validacion de pagos
		// se prepara para enviar correo de VALIDACION
		public static function sendvalidapago($factura,$direccion)
		{
			//buscamos direccion correos en datos
			return Mailer::sendvalidacionpago($direccion, 'Validacion de pago ' . $factura->FACT_NUMCONFIRM, 'detallepago', ['registro' => $factura]);
		}		
		protected function sendvalidacionpago($to, $subject, $view, $params = [])
		{
			$parametros = Configuration::getParam();

			if ($parametros['useTransport']==true) {
				$mailer           = Yii::$app->mail;
			} else {
				$mailer           = Yii::$app->mailer;
			}

			$mailer->viewPath = '@app/views/factura/correos';

			return $mailer->compose(['html' => $view, 'text' =>  $view], $params)
				->setTo($to)
				->setFrom(Yii::$app->config->get('mail.username'), 'no@reply.com')
				->setSubject($subject)
				->send();
		}
                
                // envia correo de anulacion de pagos
		// se prepara para enviar correo de anulacion
		public static function sendanulapago($factura,$direccion)
		{
			//buscamos direccion correos en datos
			return Mailer::sendanulacionpago($direccion, 'Anulación del pago ' . $factura->FACT_NUMCONFIRM, 'anulacionpago', ['registro' => $factura]);
		}		
		protected function sendanulacionpago($to, $subject, $view, $params = [])
		{
			$parametros = Configuration::getParam();

			if ($parametros['useTransport']==true) {
				$mailer           = Yii::$app->mail;
			} else {
				$mailer           = Yii::$app->mailer;
			}

			$mailer->viewPath = '@app/views/factura/correos';

			return $mailer->compose(['html' => $view, 'text' =>  $view], $params)
				->setTo([$to])
				->setFrom(Yii::$app->config->get('mail.username'), 'no@reply.com')
				->setSubject($subject)
				->send();
		}       
                
                // envia correo de validacion de pagos
		// se prepara para enviar correo de VALIDACION
		public static function sendestadocuenta($factura,$direccion)
		{
			//buscamos direccion correos en datos
			return Mailer::sendcorreoestado($direccion, 'Estado de cuenta ', 'detallepago', ['registro' => $factura]);
		}		
		protected function sendcorreoestado($to, $subject, $view, $params = [])
		{
			$parametros = Configuration::getParam();

			if ($parametros['useTransport']==true) {
				$mailer           = Yii::$app->mail;
			} else {
				$mailer           = Yii::$app->mailer;
			}

			$mailer->viewPath = '@app/views/notas/correos';

			return $mailer->compose(['html' => $view, 'text' =>  $view], $params)
				->setTo($to)
				->setFrom(Yii::$app->config->get('mail.username'), 'no@reply.com')
				->setSubject($subject)
				->send();
		}
                
		// envia correo de validacion de pagos
		// se prepara para enviar correo de VALIDACION
		public static function sendbasecorreo($basecorreo,$direccion)
		{
			//buscamos direccion correos en datos
			return Mailer::sendcorreoprueba($direccion,  $basecorreo->ASUNTO, 'basecorreo', ['registro' => $basecorreo]);
		}
                
		protected function sendcorreoprueba($to, $subject, $view, $params = [])
		{
			$parametros = Configuration::getParam();

			if ($parametros['useTransport']==true) {
				$mailer           = Yii::$app->mail;
			} else {
				$mailer           = Yii::$app->mailer;
			}

			$mailer->viewPath = '@app/views/socios/correos';

			return $mailer->compose(['html' => $view, 'text' =>  $view], $params)
				->setTo($to)
				->setFrom(Yii::$app->config->get('mail.username'), 'no@reply.com')
				->setSubject($subject)
				->send();
		}                
                
	}