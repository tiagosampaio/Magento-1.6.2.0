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

class OsStudios_PagSeguro_Model_Returns extends Mage_Core_Model_Abstract
{
        
       /**
        * Handle the parameters used in consult
        * @var (array)
        */
        protected $_params = array();
    
	/**
	 * 
	 * Consult initial date
	 * @var (datetime)
	 */
	protected $_initialDate = null;
	
	/**
	 * 
	 * Consult ending date
	 * @var (datetime)
	 */
	protected $_endingDate = null;
	
	/**
	 * 
	 * Sets the initial date to consult transactions
	 * @param (mixed) $date
	 */
	public function setDateInitial($date = null)
	{
            $this->_initialDate = $date;
            return $this;
	}
	
        /**
         *
         * Returns the initial date to consult in 'YYYY-MM-DDTHH:MM' format
         * @return (string )
         */
        public function getDateInitial()
        {
            return $this->_initialDate;
        }
        
	/**
	 * 
	 * Sets the ending date to consult transactions
	 * @param (mixed) $date
	 */
	public function setDateEnding($date = null)
	{
            $this->_endingDate = $date;
            return $this;
	}
	
        /**
         *
         * Returns the ending date to consult in 'YYYY-MM-DDTHH:MM' format
         * @return (string )
         */
        public function getDateEnding()
        {
            return $this->_endingDate;
        }
        
	/**
	 * 
	 * Get transactions URL
	 * @return (string)
	 */
	protected function getTransactionsUrl()
	{
		$url = Mage::getStoreConfig('payment/pagseguro_config/pagseguro_transactions_url', Mage::app()->getStore());
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
	 * Consults and updates the order statuses with PagSeguro
	 */
    public function consultOrderStatusBetweenDates()
    {	
		$this->_params[] = array('initialDate' => '2012-06-08T00:00');
		$this->_params[] = array('finalDate' => '2012-06-14T00:00');
		$this->_params[] = array('page' => '1');
		$this->_params[] = array('maxPageResults' => '100');
		$this->_params[] = array('email' => 'thiko_38@hotmail.com');
		$this->_params[] = array('token' => '35EA3CABB6F243059A87B8053FB4905D');
		
		$client = $this->getClient($params);
		
		$answer = $client->request();
		$body = $answer->getBody();
		
		$xml = new Varien_Simplexml_Config($body);
		$transactions = $xml->getNode('transactions/transaction');
		
		foreach( $transactions as $transaction )
		{
			Mage::log($transaction, null, 'transactions.log');
		}	
    	
    }
    
}
