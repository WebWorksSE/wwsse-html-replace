<?php
namespace WwseHtmlReplace;

use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap($e)
    {
	    $eventManager = $e->getApplication()->getEventManager ();

	    $eventManager->attach(MvcEvent::EVENT_FINISH, function(MvcEvent $e) {
		    $cfg = $e->getApplication()->getServiceManager()->get('config');

	    	if(array_key_exists('wwsse_html_replace', $cfg)) {
			    $controller = $e->getRouteMatch()->getParam('controller');
			    $action     = $e->getRouteMatch()->getParam('action');
			    $class      = $cfg['controllers']['invokables'][$controller];
			    $crowlerCfg = $cfg['wwsse_html_replace'];

			    if(array_key_exists($class, $crowlerCfg) && array_key_exists($action, $crowlerCfg[$class])) {
				    $content = $e->getResponse()->getContent();
				    $crowler = HtmlPageCrawler::create($content);

				    $e->getResponse()->setContent($crowlerCfg[$class][$action]($crowler));
			    }
		    }
	    });
    }
}
