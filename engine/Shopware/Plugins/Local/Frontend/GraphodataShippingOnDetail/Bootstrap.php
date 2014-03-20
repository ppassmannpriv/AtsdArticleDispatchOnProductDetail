<?php
class Shopware_Plugins_Frontend_GraphodataShippingOnDetail_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{ 
	private $plugin_info = array(
        'version'     => "1.0.0",
        'label'       => "graphodata AG - ATSD Versandkosten/Aufschläge auf Produktdetailseite darstellen",
        'description' => "Versandarten sperren & Versandkosten Aufschläge für Artikel - Darstellung für Produktdetailseite",
        'supplier'    => "graphodata AG",
        'autor'       => "graphodata AG",
        'support'     => "graphodata AG",
        'copyright'   => "graphodata AG",
        'link'        => 'http://www.graphodata.de',
        'source'      => null,
        'changes'     => null,
        'license'     => null,
        'revision'    => null
    );

	/**
     * Returns the current version of the plugin.
     * 
     * @return string
     */
    
    public function getVersion()
    {
        return $this->plugin_info['version'];
    }
    
    
    
    /**
     * Get (nice) name for the plugin manager list
     * 
     * @return string
     */
    
    public function getLabel()
    {
        return $this->plugin_info['label'];
    }
    

    
    /**
     * Get full information for the plugin manager list
     * @return array
     */
    
    public function getInfo()
    {
        return $this->plugin_info;
    } 

    public function install()
    {
		$this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Frontend_Detail',
            'onPostDispatchDetail'
        );
        return true;
    }
 
    public function onPostDispatchDetail(Enlight_Event_EventArgs $arguments)
    {
        /**@var $controller Shopware_Controllers_Frontend_Index*/
        $controller = $arguments->getSubject();
 
        $view = $controller->View();
		
		//Get Article
		$sArticle = $view->getAssign('sArticle');

        //Add our plugin template directory to load our slogan extension.
        $view->addTemplateDir($this->Path() . 'Views/');
        $view->extendsTemplate('frontend/plugins/gd_sod/index.tpl');

		//Assign SmartyVars
        $view->assign('shippingCost', $this->getShippingCost($sArticle));
    }
 
    private function getShippingCost($sArticle)
    {
        //get random number between 0 and the count of our slogans
		$costRaw = $this->getCostById($sArticle['articleID']);

        return $costRaw.' €';
    }

	private function getCostById($id)
	{
		$sql = "SELECT `surcharge` FROM `atsd_article_dispatch_surcharges` WHERE `articleId` = ".$id;
		$result = Shopware()->Db()->query($sql)->fetch();
		$result = $result['surcharge'] * 1.19;
		$result = number_format($result, 2, ',', '');
		
		return $result;
	}

}
 