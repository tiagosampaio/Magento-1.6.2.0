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

class OsStudios_PagSeguro_Model_Returns_Transactions_Transaction extends Mage_Core_Model_Abstract
{
    /**
     * 
     * Inquiry date
     * @var (string)
     */
    protected $_date 			= null;
    
    /**
     * 
     * Reference order ID
     * @var (string)
     */
    protected $_reference		= null;
    
    /**
     * 
     * PagSeguro's transaction code
     * @var (string)
     */
    protected $_code			= null;
    
    /**
     * 
     * Transaction type: Payment
     * @var (int)
     */
    protected $_type			= 1;
    
    /**
     * 
     * Transaction Status
     * @var (int)
     */
    protected $_status			= null;
	
    /**
     * 
     * Transaction payment method
     * @var (int)
     */
    protected $_paymentMethod	= null;
    
    /**
     * 
     * Transaction gross amount
     * @var (decimal)
     */
    protected $_grossAmount		= 0;
    
    /**
     * 
     * Transaction discount amount
     * @var (decimal)
     */
    protected $_discountAmount	= 0;
    
    /**
     * 
     * Transaction fee amount: amount of charges to PagSeguro
     * @var (decimal)
     */
    protected $_feeAmount		= 0;
    
    /**
     * 
     * Transaction net amount
     * @var (decimal)
     */
    protected $_netAmount		= 0;
    
    /**
     * 
     * Extra amount
     * @var (decimal)
     */
    protected $_extraAmount		= 0;
    
    /**
     * 
     * Last event date for the transaction
     * @var (decimal)
     */
    protected $_lastEventDate	= null;
    
    
    
    /**
     * 
     * Status: Waiting for the payment
     * @var (int)
     */
    const STATUS_WAITING_PAYMENT = 1;
    
    /**
     * 
     * Status: Payment is being analysed
     * @var (int)
     */
    const STATUS_ANALYSIS = 2;
    
    /**
     * 
     * Status: The transaction was paid
     * @var (int)
     */
    const STATUS_PAID = 3;
    
    /**
     * 
     * Status: The transaction was paid and the money is available to shopper in PagSeguro 
     * @var (int)
     */
    const STATUS_AVAILABLE = 4;
    
    /**
     * 
     * Status: A dispute was opened by customer
     * @var (int)
     */
    const STATUS_IN_DISPUTE = 5;
    
    /**
     * 
     * Status: The money was given back to customer
     * @var (int)
     */
    const STATUS_RETURNED = 6;
    
    /**
     * 
     * Status: The transaction was canceled for any reason
     * @var (int)
     */
    const STATUS_CANCELED = 7;
	
	
    
}
