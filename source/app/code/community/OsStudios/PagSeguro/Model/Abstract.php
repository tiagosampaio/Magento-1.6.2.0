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

abstract class OsStudios_PagSeguro_Model_Abstract extends Mage_Payment_Model_Method_Abstract
{
    
    protected $_order 				= null;
    
    const PAGSEGURO_LOG_FILENAME		= 'osstudios_pagseguro.log';
    
    const PAGSEGURO_STATUS_COMPLETE		= 'Completo';
    const PAGSEGURO_STATUS_WAITING_PAYMENT	= 'Aguardando Pagto';
    const PAGSEGURO_STATUS_APPROVED		= 'Aprovado';
    const PAGSEGURO_STATUS_ANALYSING		= 'Em Análise';
    const PAGSEGURO_STATUS_CANCELED		= 'Cancelado';
    const PAGSEGURO_STATUS_RETURNED		= 'Devolvido';
    
    
    /**
     *  Return Order
     *
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
        	return false;
        }
        return $this->_order;
    }
	
    
    /**
     * 
     *  Set Current Order
     *
     *  @param Mage_Sales_Model_Order $order
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
    	if(!$this->_order)
    	{
    		$this->_order = $order;
    	}
        return $this;
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
     * Returns the Current Store
     * 
     * @return string
     */
    public function getStore()
    {
    	if($this->getOrder()) {
    		$store = $this->getOrder()->getStore();
    	} else {
    		$store = Mage::app()->getStore();
    	}
    	
    	return $store;
    }
    
    
    /**
     * 
     * Returns the URL for payments on PagSeguro
     * 
     * @return string
     */ 
    public function getPagSeguroUrl()
    {
        $url = Mage::getStoreConfig('payment/pagseguro_config/pagseguro_url', $this->getStore());
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
        $url = Mage::getStoreConfig('payment/pagseguro_config/pagseguro_npi_url', $this->getStore());
    	if(!$url) {
    		Mage::throwException( Mage::helper('pagseguro')->__('The PagSeguro NPI URL could not be retrieved.') );
    	}
    	return $url;
    }
    
    
    /**
     * getPagSeguroBoletoUrl
     * 
     * Returns the URL to generate the billets of PagSeguro
     * 
     * @param string $transactionId = PagSeguro Transaction ID
     * 
     * @return string
     */ 
    public function getPagSeguroBoletoUrl($transactionId, $escapeHtml = true)
    {
        $url = Mage::getStoreConfig('payment/pagseguro_config/pagseguro_billet_url', $this->getStore());
    	if(!$url) {
    		Mage::throwException( Mage::helper('pagseguro')->__('The PagSeguro Billet URL could not be retrieved.') );
    	}
    	
        $url .= '?resizeBooklet=n&code=' . $transactionId;
        if ($escapeHtml) {
            $url = Mage::helper('pagseguro')->escapeHtml($url);
        }
        return $url;
    }
    
}
