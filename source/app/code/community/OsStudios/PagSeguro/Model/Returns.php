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

class OsStudios_PagSeguro_Model_Returns extends OsStudios_PagSeguro_Model_Abstract
{
	
	const PAGSEGURO_RETURN_RESPONSE_SUCCESS = true;
	const PAGSEGURO_RETURN_RESPONSE_FAIL = false;
	const PAGSEGURO_RETURN_RESPONSE_UNAUTHORIZED = 'Unauthorized';
	const PAGSEGURO_RETURN_RESPONSE_AUTHORIZED = 'Authorized';
	const PAGSEGURO_RETURN_RESPONSE_ERROR = 'Process Error';
	
    /**
     * Default return from PagSeguro
     * 
     * @var (int)
     */
	const PAGSEGURO_RETURN_TYPE_DEFAULT = 1;
	
	/**
     * Api return from PagSeguro
     * 
     * @var (int)
     */
	const PAGSEGURO_RETURN_TYPE_API = 2;
	
	/**
     * Request a consult in PagSeguro
     * 
     * @var (int)
     */
	const PAGSEGURO_RETURN_TYPE_CONSULT = 3;
	
	/**
	 * Handle the return type
	 * 
	 * @var (const)
	 */
	protected $_returnType = null;
	
	/**
	 * Handle the post information
	 * 
	 * @var (mixed)
	 */
	protected $_post = null;
	
	/**
	 * Handle the process result
	 * 
	 * @var (bool)
	 */
	protected $_success = false;
	
	/**
	 * Handle the response result
	 * 
	 * @var (mixed)
	 */
	protected $_response = null;
	
	
	/**
	 * Sets the post data
	 * 
	 * @param (mixed) $post
	 * @return OsStudios_PagSeguro_Model_Returns
	 */
	public function setPostData($post)
	{
		$this->_post = $post;
		$this->setPost($this->_post);
		return $this;
	}
	
	
	/**
	 * Sets the return type
	 * 
	 * @param (int) $type
	 * @return OsStudios_PagSeguro_Model_Returns
	 */
	public function setReturnType($type = self::PAGSEGURO_RETURN_TYPE_DEFAULT)
	{
		$this->_returnType = $type;
		return $this;
	}
	
	
	/**
	 * Return true if the returned has processed
	 * 
	 * @return (bool)
	 */
	public function isSuccess()
	{
		return $this->_success;
	}
	
	
	/**
	 * Return response of the return
	 * 
	 * @return (bool)
	 */
	public function getResponse()
	{
		return $this->_response;
	}
	
	
	/**
	 * Runs before validation
	 */
	protected function _beforeValidate()
	{
		
	}
	
	
	/**
	 * Runs after validation
	 */
	protected function _afterValidate()
	{
		
	}
	
	
	/**
	 * Runs before validation
	 */
	protected function _beforeReturns()
	{
		
	}
	
	
	/**
	 * Runs after validation
	 */
	protected function _afterReturns()
	{
		
	}
	
	
	/**
	 * Validates the received post in PagSeguro
	 * 
	 * @return (int)
	 */
	protected function _validate()
	{
		$this->_beforeValidate();
		
		$post = $this->getPost();
		
		if( !empty($post)) {
			
			$credentials = Mage::getModel('pagseguro/credentials');
					
			$post['encoding'] = 'utf-8';
			$post['Comando'] = 'validar';
			$post['Token'] = $credentials->getToken();
			
			$client = new Zend_Http_Client($this->getPagSeguroNPIUrl());
			
			if( $this->getConfigData('use_curl') ) {
				$adapter = new Zend_Http_Client_Adapter_Curl();
				
				$config = array('timeout' => 30,
								'curloptions' => array(
									CURLOPT_SSL_VERIFYPEER => false
								));
				$adapter->setConfig($config);
				$client->setAdapter($adapter);
			}
			
			try {
				
				$client->setMethod(Zend_Http_Client::POST)
					   ->setParameterPost($post);
				
				$content = $client->request();
				$return = $content->getBody();
				
				$this->log($return);
				
			} catch (Mage_Core_Exception $e) {
				$this->log($e->getMessage());
			} catch (Exception $e) {
				$this->log($e->getMessage());
			}
			
			$result = (strcmp($return, 'VERIFICADO') == 0);
		}
		
		$this->_afterValidate();
		
		return $result;
		//return true;
	}
	
	
	/**
	 * Identifies and process the correct return
	 * 
	 * @return OsStudios_PagSeguro_Model_Returns
	 */
	public function runReturns()
	{
		
		$this->_beforeReturns();
		
		$type = $this->_returnType;
		$post = $this->getPost();
		
		switch ($type)
		{
			/**
			 * Returns from PagSeguro API
			 */
			case self::PAGSEGURO_RETURN_TYPE_API:
				
				$model = Mage::getModel('pagseguro/returns_types_api');
				$this->_response = $model->processReturn()->getResponse();
				
				$errArray = array(self::PAGSEGURO_RETURN_RESPONSE_UNAUTHORIZED, self::PAGSEGURO_RETURN_RESPONSE_ERROR);
				
				if(in_array($this->_response, $errArray)) {
					$this->_success = false;
				} else {
					$this->_success = true;
				}
				
				break;
			
			/**
			 *  Self consulting 
			 */
			case self::PAGSEGURO_RETURN_TYPE_CONSULT:
				
				$stop = false;
				
				$model = Mage::getModel('pagseguro/returns_types_consult');
				
				$this->_response = $model->processReturn()->getResponse();
				
				if($this->_response == self::PAGSEGURO_RETURN_RESPONSE_UNAUTHORIZED) {
					$errMsg = $this->__('The consult was not authorized by PagSeguro.');
					$stop = true;
				} elseif ($this->_response == self::PAGSEGURO_RETURN_RESPONSE_ERROR) {
					$errMsg = $this->__('PagSeguro has returned an error.');
					$stop = true;
				}
				
				if(Mage::getSingleton('admin/session') && $stop) {					
					Mage::getSingleton('adminhtml/session')->addError($errMsg);
					return $this;
				}
				
				$this->_success = true;
				
				break;
				
			/**
			 * Automatic return from PagSeguro
			 */
			case self::PAGSEGURO_RETURN_TYPE_DEFAULT:
			default:
				
				if($this->_validate()) {
					$model = Mage::getModel('pagseguro/returns_types_default');
					$this->_response = $model->setPostData($post)->processReturn()->getResponse();
				}
				
				$errArray = array(self::PAGSEGURO_RETURN_RESPONSE_UNAUTHORIZED, self::PAGSEGURO_RETURN_RESPONSE_ERROR);
				
				if(in_array($this->_response, $errArray)) {
					$this->_success = false;
				} else {
					$this->_success = true;
				}
				
				break;
		}
		
		$this->_afterReturns();
		
		return $this;
	}
	
}
