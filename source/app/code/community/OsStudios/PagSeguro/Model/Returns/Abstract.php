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

abstract class OsStudios_PagSeguro_Model_Returns_Abstract extends Mage_Core_Model_Abstract
{
    
    /**
     * If result was seccessfully returned handle true
     * @var boolean
     */
    protected $_success = false;
    
    public function wasSuccess()
    {
        return $this->_success;
    }
    
    protected function setSuccess($bool = false)
    {
        $this->_success = $bool;
        return $this;
    }
    
    
}
