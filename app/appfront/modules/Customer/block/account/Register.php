<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
namespace fecshop\app\appfront\modules\Customer\block\account;
use Yii;
use fec\helpers\CModule;
use fec\helpers\CRequest;
use yii\base\InvalidValueException;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Register {
	
	public function getLastData($param){
		$firstname 		= isset($param['firstname']) ? $param['firstname'] : '';
		$lastname 		= isset($param['lastname']) ? $param['lastname'] : '';
		$email 			= isset($param['email']) ? $param['email'] : '';
		$registerParam = \Yii::$app->getModule('customer')->params['register'];
		$registerPageCaptcha = isset($registerParam['registerPageCaptcha']) ? $registerParam['registerPageCaptcha'] : false;
		
		return [
			'firstname'		=> $firstname,
			'lastname'		=> $lastname,
			'email'			=> $email,
			'is_subscribed'	=> $is_subscribed,
			'minNameLength' => Yii::$service->customer->getRegisterNameMinLength(),
			'maxNameLength' => Yii::$service->customer->getRegisterNameMaxLength(),
			'minPassLength' => Yii::$service->customer->getRegisterPassMinLength(),
			'maxPassLength' => Yii::$service->customer->getRegisterPassMaxLength(),
			'registerPageCaptcha' => $registerPageCaptcha,
		];
	}
	
	public function register($param){
		
		$captcha = $param['captcha'];
		$registerParam = \Yii::$app->getModule('customer')->params['register'];
		$registerPageCaptcha = isset($registerParam['registerPageCaptcha']) ? $registerParam['registerPageCaptcha'] : false;
		# 如果开启了验证码，但是验证码验证不正确就报错返回。
		if($registerPageCaptcha && !$captcha){
			Yii::$service->page->message->addError(['Captcha can not empty']);
			return;
		}else if($captcha && $registerPageCaptcha && !\Yii::$service->helper->captcha->validateCaptcha($captcha)){
			Yii::$service->page->message->addError(['Captcha is not right']);
			return;
		}
		
		Yii::$service->customer->register($param);
		
		$errors = Yii::$service->helper->errors->get(true);
		if($errors){
			if(is_array($errors) && !empty($errors)){
				foreach($errors as $error){
					if(is_array($error) && !empty($error)){
						foreach($error as $er){
							Yii::$service->page->message->addError($er);
						}
					}
				}
			} 
		}else{
			# 发送注册邮件
			$this->sendRegisterEmail($param);
			return true;
		}
	}
	
	/**
	 * 发送登录邮件
	 */
	public function sendRegisterEmail($param){
		if($param){
			$mailerConfig = Yii::$app->params['mailer'];
			$mailer_class = isset($mailerConfig['mailer_class']) ? $mailerConfig['mailer_class'] : '';
			if($mailer_class){
				forward_static_call(
					[$mailer_class , 'sendRegisterEmail'],
					$param
				);
			}
		}
	}
}



