<?php
class OsStudios_Multicarrier_Model_Carrier_Braspress extends OsStudios_Multicarrier_Model_Carrier_Abstract
{
	/**
     * _code property
     *
     * @var string
     */
	protected $_code = 'braspress';
	
	/**
     * _result property
     *
     * @var Mage_Shipping_Model_Rate_Result
     */
    protected $_result = null;
	
    public function __construct()
    {
    	$this->setGris( Mage::getStoreConfig("carriers/{$this->_code}/gris") );
    	$this->setToll( Mage::getStoreConfig("carriers/{$this->_code}/toll") );
    	$this->setTas( Mage::getStoreConfig("carriers/{$this->_code}/tas") );
    	$this->setAdm( Mage::getStoreConfig("carriers/{$this->_code}/administration") );
    }
    
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		// skip if not enabled
        if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')){
            return false;
        }
        
        $this->setRequest($request);
        
        $result = Mage::getModel('shipping/rate_result');
        
        $handling = $this->_calculate();
        
        if(Mage::getStoreConfig('carriers/'.$this->_code.'/handling') >0){
            $handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
    	}
        if(Mage::getStoreConfig('carriers/'.$this->_code.'/handling_type') == 'P' && $request->getPackageValue() > 0){
            $handling = $request->getPackageValue() * $handling;
        }
 
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));
        
        /* Use method name */
        
        $method->setMethod('delivery');
        $method->setMethodTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/methodtitle'));
        $method->setCost($handling);
        $method->setPrice($handling);
        $result->append($method);
        
        return $result;
	}
	
	private function _calculate()
	{
		$request = $this->getRequest();
		
		$gris = $this->getGris();
		$toll = $this->getToll();
		$tas  = $this->getTas();
		$adm  = $this->getAdm();
		
		$pkgVal = $request->getPackageValue();
		$pkgWgt = $request->getPackageWeight();
		
		$valInc = 0.003;
		
		if($pkgWgt > 70) {
			$wgtInc = $pkgWgt * 0.99;
		} else {
			$wgtInc = 13.99;
		}
		
		$result = (($pkgVal * $valInc) + ($pkgVal * $gris) + ($toll + $tas + $wgtInc)) * (1 + $adm);
		
		return $result;
	}
	
}