<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
namespace fecshop\app\appfront\helper\mailer;
use Yii;
use fec\helpers\CConfig;
use fec\controllers\FecController;
use yii\base\InvalidValueException;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Email
{
	
	/**
	 * 得到联系我们的邮箱地址
	 */
	public static function contactsEmailAddress(){
		$mailerConfig =  Yii::$app->params['mailer'];
		if(isset($mailerConfig['contacts']['emailAddress'])){
			return $mailerConfig['contacts']['emailAddress'];
		}
	}
	/**
	 * 得到Store Name
	 */
	public static function storeName(){
		$mailerConfig =  Yii::$app->params['mailer'];
		if(isset($mailerConfig['storeName'])){
			return $mailerConfig['storeName'];
		}
	}
	public static function contactsPhone(){
		$mailerConfig =  Yii::$app->params['mailer'];
		if(isset($mailerConfig['phone'])){
			return $mailerConfig['phone'];
		}
	}
	
	/**
	 * 通过block 和 view 得到邮件内容
	 */
	public static function getSubjectAndBody($block,$viewPath,$langCode='',$params=[]){
		if(!$langCode){
			$langCode = Yii::$service->store->currentLangCode;
		}
		if(!$langCode){
			Yii::$service->helper->errors->add('langCode is empty');
			return ;
		}
		
			
		$bodyViewFile	= $viewPath.'/body_'.$langCode.'.php';
		$bodyConfigKey = [
			'class' => $block,
			'view'  => $bodyViewFile,
		];
		if(!empty($params)){
			$bodyConfigKey['params'] = $params;
		}
		$subjectViewFile	= $viewPath.'/subject_'.$langCode.'.php';
		$subjectConfigKey = [
			'class' => $block,
			'view'  => $subjectViewFile,
		];
		$emailSubject = Yii::$service->page->widget->render($subjectConfigKey,$parentThis);
		$emailBody = Yii::$service->page->widget->render($bodyConfigKey,$parentThis);
		
		return [$emailSubject,$emailBody];
	}
	
	
	/**
	 * @property $toEmail | String   send to email address.
	 * 客户注册用户发送邮件
	 */
	public static function sendRegisterEmail($param){
		$toEmail = $param['email'];
		$registerParam = Yii::$app->getModule('customer')->params['register'];
		if(isset($registerParam['email']['enable']) && $registerParam['email']['enable']){
			$mailerConfigParam = '';
			if(isset($registerParam['email']['mailerConfig']) && $registerParam['email']['mailerConfig']){
				$mailerConfigParam = $registerParam['email']['mailerConfig'];	
			}
			if(isset($registerParam['email']['block']) && $registerParam['email']['block']){
				$block = $registerParam['email']['block'];
			}
			if(isset($registerParam['email']['viewPath']) && $registerParam['email']['viewPath']){
				$viewPath = $registerParam['email']['viewPath'];
			}
			if($block && $viewPath){
				list($subject,$htmlBody) = self::getSubjectAndBody($block,$viewPath,'',$param);
				$sendInfo = [
					'to' 		=> $toEmail,
					'subject' 	=> $subject,
					'htmlBody' => $htmlBody,
					'senderName'=> Yii::$service->store->currentStore,
				];
				//var_dump($sendInfo);exit;
				Yii::$service->email->send($sendInfo,$mailerConfigParam);
	
			}
		
		}
	}
	
	/**
	 * 客户登录账号发送邮件
	 */
	public static function sendLoginEmail($toEmail){
		$registerParam = Yii::$app->getModule('customer')->params['login'];
		if(isset($registerParam['email']['enable']) && $registerParam['email']['enable']){
			$mailerConfigParam = '';
			if(isset($registerParam['email']['mailerConfig']) && $registerParam['email']['mailerConfig']){
				$mailerConfigParam = $registerParam['email']['mailerConfig'];	
			}
			if(isset($registerParam['email']['block']) && $registerParam['email']['block']){
				$block = $registerParam['email']['block'];
			}
			if(isset($registerParam['email']['viewPath']) && $registerParam['email']['viewPath']){
				$viewPath = $registerParam['email']['viewPath'];
			}
			if($block && $viewPath){
				list($subject,$htmlBody) = self::getSubjectAndBody($block,$viewPath);
				$sendInfo = [
					'to' 		=> $toEmail,
					'subject' 	=> $subject,
					'htmlBody' 	=> $htmlBody,
					'senderName'=> Yii::$service->store->currentStore,
				];
				Yii::$service->email->send($sendInfo,$mailerConfigParam);
			}
		}
	}
	
	
	/**
	 * 客户登录账号发送邮件
	 */
	public static function sendForgotPasswordEmail($param){
		$toEmail = $param['email'];
		$forgotPasswordParam = Yii::$app->getModule('customer')->params['forgotPassword'];
		$mailerConfigParam = '';
		if(isset($forgotPasswordParam['email']['mailerConfig']) && $forgotPasswordParam['email']['mailerConfig']){
			$mailerConfigParam = $forgotPasswordParam['email']['mailerConfig'];	
		}
		if(isset($forgotPasswordParam['email']['block']) && $forgotPasswordParam['email']['block']){
			$block = $forgotPasswordParam['email']['block'];
		}
		if(isset($forgotPasswordParam['email']['viewPath']) && $forgotPasswordParam['email']['viewPath']){
			$viewPath = $forgotPasswordParam['email']['viewPath'];
		}
		if($block && $viewPath){
			list($subject,$htmlBody) = self::getSubjectAndBody($block,$viewPath,'',$param);
			$sendInfo = [
				'to' 		=> $toEmail,
				'subject' 	=> $subject,
				'htmlBody' => $htmlBody,
				'senderName'=> Yii::$service->store->currentStore,
			];
			//var_dump($sendInfo);exit;
			Yii::$service->email->send($sendInfo,$mailerConfigParam);

		}
		
	}
	
	
	
}
