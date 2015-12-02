<?php

abstract class Paymentwall_Base
{
	/**
	 * Paymentwall library version
	 */
	const VERSION = '1.0.0';

	/**
	 * API types
	 */
	const API_VC = 1;
	const API_GOODS = 2;
	const API_CART = 3;

	/**
	 * Controllers for APIs
	 */
	const CONTROLLER_PAYMENT_VIRTUAL_CURRENCY = 'ps';
	const CONTROLLER_PAYMENT_DIGITAL_GOODS = 'subscription';
	const CONTROLLER_PAYMENT_CART = 'cart';

	/**
	 * Signature versions
	 */
	const DEFAULT_SIGNATURE_VERSION = 3;
	const SIGNATURE_VERSION_1 = 1;
	const SIGNATURE_VERSION_2 = 2;
	const SIGNATURE_VERSION_3 = 3;

	protected $errors = array();

	/**
	 * Paymentwall API type
	 * @param int $apiType
	 */
	public static $apiType;

	/**
	 * Paymentwall application key - can be found in your merchant area
	 * @param string $appKey
	 */
	public static $appKey;

	/**
	 * Paymentwall secret key - can be found in your merchant area
	 * @param string $secretKey
	 */
	public static $secretKey;

	/**
	 * @param int $apiType API type, Paymentwall_Base::API_VC for Virtual Currency, Paymentwall_Base::API_GOODS for Digital Goods
	 * Paymentwall_Base::API_CART for Cart, more details at http://paymentwall.com/documentation
	 */ 
	public static function setApiType($apiType)
	{
		self::$apiType = $apiType;
	}

	public static function getApiType()
	{
		return self::$apiType;
	}

	/**
	 * @param string $appKey application key of your application, can be found inside of your Paymentwall Merchant Account
	 */ 
	public static function setAppKey($appKey)
	{
		self::$appKey = $appKey;
	}

	public static function getAppKey()
	{
		return self::$appKey;
	}

	/**
	 * @param string $secretKey secret key of your application, can be found inside of your Paymentwall Merchant Account
	 */ 
	public static function setSecretKey($secretKey)
	{
		self::$secretKey = $secretKey;
	}

	public static function getSecretKey()
	{
		return self::$secretKey;
	}

	/**
	 * Fill the array with the errors found at execution
	 *
	 * @param $err
	 * @return int
	 */
	protected function appendToErrors($err)
	{
		return array_push($this->errors, $err);
	}

	/**
	 * Return errors
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Return error summary 
	 *
	 * @return string
	 */
	public function getErrorSummary()
	{
		return implode("\n", $this->getErrors());
	}
}