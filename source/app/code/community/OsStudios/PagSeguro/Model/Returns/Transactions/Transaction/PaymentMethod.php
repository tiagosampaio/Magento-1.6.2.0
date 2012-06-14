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

class OsStudios_PagSeguro_Model_Returns_Transactions_Transaction_PaymentMethod extends Mage_Core_Model_Abstract
{
    /**
     * 
     * Paid with Credit Card
     * @var (int)
     */
	const PAYMENT_TYPE_CC = 1;
	
	/**
	 * 
	 * Paid with Billet
	 * @var (int)
	 */
	const PAYMENT_TYPE_BILLET = 2;
	
	/**
	 * 
	 * Paid with Online Debit (TEF)
	 * @var (int)
	 */
	const PAYMENT_TYPE_DEBIT = 3;
	
	/**
	 * 
	 * Paid with PagSeguro Account Credits
	 * @var (int)
	 */
	const PAYMENT_TYPE_PAGSEGUROCREDIT = 4;
	
	/**
	 * 
	 * Paid with Oi Paggo via Celphones
	 * @var (int)
	 */
	const PAYMENT_TYPE_OIPAGGO = 5;
	
}
