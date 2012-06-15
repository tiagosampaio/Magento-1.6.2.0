<?php
/**
 * Os Studios PagSeguro Payment Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OsStudios
 * @package    OsStudios_PagSeguro
 * @copyright  Copyright (c) 2012 Os Studios (www.osstudios.com.br)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Tiago Sampaio <tiago.sampaio@osstudios.com.br>
 */

abstract class OsStudios_PagSeguro_Model_Abstract extends Mage_Core_Model_Abstract
{
   
	const PAGSEGURO_DATE_FORMAT = 'Y-m-d\TH:i';
	const PAGSEGURO_LOG_FILENAME = 'osstudios_pagseguro.log';
	
	protected $_configPrefix = 'payment/pagseguro_config/';
	protected $_credentials = null;
	protected $_store = null;
	protected $_coreDate = null;
	
	/**
     * Retrieve information from PagSeguro configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = $this->_configPrefix.$field;
        return Mage::getStoreConfig($path, $storeId);
    }
	
    
	/**
     * 
     * Registry any event/error log.
     * 
     * @return OsStudios_PagSeguro_Model_Payment
     * 
     * @param string $message
     * @param integer $level
     * @param string $file
     * @param bool $forceLog
     */
    public function log($message, $level = null, $file = self::PAGSEGURO_LOG_FILENAME, $forceLog = false) {
    	if(Mage::getStoreConfig('payment/pagseguro_config/log_enable', $this->getStore()))
    	{
            if( is_array($message) ) {
                    Mage::log($message, $level, $file, $forceLog);
            } else {
                    Mage::log("PagSeguro: " . $message, $level, $file, $forceLog);
            }
    	}
    	
    	return $this;
    }
    
    
    /**
     * 
     * Returns core date model from Magento
     * @return Mage_Core_Model_Date
     */
    protected function getCoreDate()
    {
    	if(!$this->_coreDate) {
    		$this->_coreDate = Mage::getModel('core/date');
    	}
    	return $this->_coreDate;
    }
    
    
    /**
     * 
     * Returns the Current Store
     * 
     * @return string
     */
    public function getStore()
    {
    	if(!$this->_store) {
    		$this->_store = Mage::app()->getStore(); 
    	}
    	return $this->_store;
    }
    
    
    /**
     * 
     * Returns the URL for payments on PagSeguro
     * 
     * @return string
     */ 
    public function getPagSeguroUrl()
    {
        $url = $this->getConfigData('pagseguro_url');
    	if(!$url) {
    		Mage::throwException( Mage::helper('pagseguro')->__('The PagSeguro URL could not be retrieved.') );
    	}
    	
    	return $url;
    }
    
    
    /**
     * 
     * Returns the Payment Notification URL of PagSeguro
     * 
     * @return string
     */ 
    public function getPagSeguroNPIUrl()
    {
        $url = $this->getConfigData('pagseguro_npi_url');
    	if(!$url) {
    		Mage::throwException( Mage::helper('pagseguro')->__('The PagSeguro NPI URL could not be retrieved.') );
    	}
    	return $url;
    }
    
    
    /**
     * 
     * Returns the URL to generate the billets of PagSeguro
     * 
     * @param string $transactionId = PagSeguro Transaction ID 
     * @return (string)
     */ 
    public function getPagSeguroBoletoUrl($transactionId, $escapeHtml = true)
    {
        $url = $this->getConfigData('pagseguro_billet_url');
    	if(!$url) {
    		Mage::throwException( Mage::helper('pagseguro')->__('The PagSeguro Billet URL could not be retrieved.') );
    	}
    	
        $url .= '?resizeBooklet=n&code=' . $transactionId;
        if ($escapeHtml) {
            $url = Mage::helper('pagseguro')->escapeHtml($url);
        }
        return $url;
    }
    
    
	/**
	 * 
	 * Returns transactions URL
	 * @return (string)
	 */
	protected function getTransactionsUrl()
	{
		$url = $this->getConfigData('pagseguro_transactions_url');
		if(!$url) {
			Mage::throwException(Mage::helper('pagseguro')->__('Unable to retrieve transactions URL from module configuration.'));
		}
		return $url;
	}
    
	
	/**
	 * 
	 * Get Zend Http Client
	 * @param (array) $params
	 * @param (Zend_Http_Client::GET or Zend_Http_Client::POST) $type
	 * @return Zend_Http_Client
	 */
	protected function getClient($params = array(), $type = Zend_Http_Client::GET)
	{
		$client = new Zend_Http_Client($this->getTransactionsUrl());
		$client->setMethod(Zend_Http_Client::GET)
			   ->setParameterGet($params);
			   
		return $client;
	}
	
	
	/**
	 * 
	 * Returns credentials
	 * @return OsStudios_PagSeguro_Model_Credentials
	 */
	protected function getCredentials()
	{
		if(!$this->_credentials) {
			$this->_credentials = Mage::getModel('pagseguro/credentials');
		}
		return $this->_credentials;
	}
	
	
	/**
	 * 
	 * Extends translation functionality
	 */
	protected function __($string)
	{
		return Mage::helper('pagseguro')->__($string);
	}
	
	protected function _redirect($path = '', $params = array())
	{
		Mage::app()->getResponse()->setRedirect(Mage::getUrl($path, $params));
	}
}
