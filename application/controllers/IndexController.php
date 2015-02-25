<?php

class IndexController extends Zend_Controller_Action
{

    public function init() {
    	
    }

    public function indexAction() {
    	
    	$this->view->form = $this->getForm();

    }

    
    
    protected function getForm(){
    	
    	$ASurl = $this->getRequest()->getParam('url');
    	
    	//TODO
    	//Takes a pre-made assertion
    	$assertion = file_get_contents(realpath(APPLICATION_PATH . '/assertion/assertionOk2.xml'));
    	
    	require_once realpath(APPLICATION_PATH . '/../library/Saml/lib/xmlseclibs/xmlseclibs.php');
    	
    	try {
    		$key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type' => 'private'));
    		$key->loadKey(realpath(APPLICATION_PATH . '/key/roleserver.key'), true, false);
    		
    		$sigNode = new XMLSecurityDSig();
    		
    		$doc = new DOMDocument();
    		@$doc->loadXML($assertion);
    		
    		$subject = $doc->getElementsByTagName("Subject")->item(0);
    		$sigNode->setCanonicalMethod('http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
    		$sigNode->addReference($subject, XMLSecurityDSig::SHA256);
    		$sigNode->add509Cert(realpath(APPLICATION_PATH . '/key/roleserver.crt'), true, true);
    		$sigNode->sign($key);
    		$sigNode->canonicalizeSignedInfo();
    		
    		//$pubkey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type' => 'public'));
    		//$pubkey->loadKey(realpath(APPLICATION_PATH . '/key/roleserver.crt'), true, true);
    		
    		
    		$assertionNode = $doc->getElementsByTagName("Assertion")->item(0);
    		$sigNode->appendSignature($assertionNode);
    		
    		$assertion64 = base64_encode($doc->saveXML());
    	}
    	catch(Exception $e){var_dump($e->getMessage()); die();}
    	
    	
    	$form = new Application_Form_RoleLoginForm(array(
    			'action' => $ASurl,
    			'method' => 'post',
    	));
    	
    	$values = array(
    			'assertion' => $assertion64
    	);
    	$form->injectRequestValues($values);
    	
    	return $form;
    }

}

