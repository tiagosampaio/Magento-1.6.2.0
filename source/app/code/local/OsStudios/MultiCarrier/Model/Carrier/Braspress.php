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
    	$this->setGris( $this->getConfigData('gris') );
    	$this->setToll( $this->getConfigData('toll') );
    	$this->setTas( $this->getConfigData('tas') );
    	$this->setAdm( $this->getConfigData('administration') );
    }
    
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		// skip if not enabled
        if (!$this->getConfigData('active')){
            return false;
        }
        
        $this->setRequest($request);
        
        $result = Mage::getModel('shipping/rate_result');
        
        $handling = $this->_calculate();
 
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        
        /* Use method name */
        
        //$method->setMethod('delivery');
        $method->setMethod('shipping');
        $method->setMethodTitle($this->getConfigData('methodtitle'));
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
		
		/*
		$frete = $this->CalcFreteBraspress('22527311000146', '2', '06321530', '06321001', '34686911818', '58', '275.60', '2', '1');
		Mage::log( $frete, null, 'frete.log' );
		*/
		
		return $result;
	}
	
	
	/* As Exemple */
	function CalcFreteBraspress($Cnpj,$EmpresaTransp,$CepLocal,$CepDestino,$CpfDestino,$Peso,$Valor,$QtdeVolumes,$TipoFrete)
	{
	    $LinkCalcFrete = "http://tracking.braspress.com.br/trk/trkisapi.dll/PgCalcFrete_XML?param=$Cnpj,$EmpresaTransp,$CepLocal,$CepDestino,$Cnpj,$CpfDestino,$TipoFrete,$Peso,$Valor,$QtdeVolumes";
	    return simplexml_load_file($LinkCalcFrete);
	}
	
	
}