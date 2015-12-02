<?php
/**
 * @package		Donations
 * @author		RenildoMarcio
 * @copyright	(c) 2015 A3LBR
 */
 
namespace IPS\donate\Gateway;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

require_once "paymentwall/Paymentwall.php";

/**
 * Paymentwall Gateway
 */
class _paymentwall extends \IPS\donate\Gateway
{	
	/**
	 * Gateway URL
	 */
	public function gatewayURL()
	{    
        return NULL;   
    }
    
	/**
	 * Init Paymentwall
	 */         
	protected function initPaymentwall( $app_key='', $secret_key='' )
    {
        \Paymentwall_Base::setApiType(\Paymentwall_Base::API_GOODS);        
        \Paymentwall_Base::setAppKey($app_key); 
        \Paymentwall_Base::setSecretKey($secret_key);
	}    
    
	/**
	 * Payment Screen
	 */
	public function paymentScreen( \IPS\Helpers\Form $form, $member, $donation )
	{
	    /* Get any gateway settings */
		$settings = json_decode( $this->settings, TRUE );  
        
        /* We need this before continuing */
        if( !isset( $settings['api_key'] ) OR !isset( $settings['api_secretkey'] ) OR !isset( $settings['widget_code'] ) )
        {
            return $form;
        }
        
        /* Init paymentwall */
        $this->initPaymentwall( $settings['api_key'], $settings['api_secretkey'] );

        /* Setup widget */
		$productNames[] = \IPS\Member::loggedIn()->language()->get('forum_donation');

		$product = new \Paymentwall_Product( "D1", $donation['amount'], $donation['currency'], implode( ',', $productNames ) );
		$widget  = new \Paymentwall_Widget( \IPS\Member::loggedIn()->member_id, $settings['widget_code'], array( $product ), array( 'goal' => $donation['goal'] )  );

        /* Add widget to form */
        $form->addHtml( $widget->getHtmlCode() );	        

		return $form;
	}
    
	/**
	 * Process payment fields
	 */    
	public function process( $result=array() )
	{ 	   
	    $paymentFields = array( 'member_id'        => isset( $result['uid'] ) ? (int) $result['uid'] : 0,
                                'amount'           => $result['amount'],
                                'currency'         => isset( $result['currencyCode'] ) ? $result['currencyCode'] : 0,
                                'goal'             => isset( $result['goal'] ) ? (int) $result['goal'] : 0,                                      
                                'status'           => 1,
                                'gateway_email'    => 'paymentwall',     
                                'gateway_receiver' => 'paymentwall',                           
                                'anonymous'        => 0, 
                                'anonymous_amount' => 0,
                                'txn_id'           => $result['ref'],      
                                'note'             => '',   
                                'fees'             => 0                                                                 
                              );
       
        return $paymentFields;	   
    }    
	
	/**
	 * Authorize Payment
	 */
	public function auth( $donation )
	{
	    /* Get any gateway settings */
		$settings = json_decode( $this->settings, true );

 		self::initPaymentwall( $settings['api_key'], $settings['api_secretkey']);
		$params = $_GET;

		$pingback = new \Paymentwall_Pingback( $params, $_SERVER['REMOTE_ADDR'] ); 
        
		$result = array();
		if ($pingback->validate(true)) 
        {
			if ($pingback->isDeliverable()) 
            {
				//$result = array( 'id' => $params['id'], 'status' => 'okay', 'amount' => $params['amount'], 'gw_id' => $params['ref'] );
			} 
            elseif ($pingback->isCancelable())
            { 
		 
			}		
            echo "OK";
		} 
        else 
        {
			echo ($pingback->getErrorSummary());die();
		}
		
		return $params; 			
	}
}