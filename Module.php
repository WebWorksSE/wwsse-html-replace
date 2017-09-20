<?php
namespace WwseHtmlReplace;

use Wa72\HtmlPageDom\HtmlPageCrawler;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap($e)
    {
	    $eventManager = $e->getApplication()->getEventManager ();

	    $eventManager->attach(MvcEvent::EVENT_FINISH, function(MvcEvent $e) {
		    $cfg = $e->getApplication()->getServiceManager()->get('config');

	    	if(array_key_exists('wwsse_html_replace', $cfg)) {
			    $route = $e->getRouteMatch();

			    if(!is_null($route)) {
				    $controller = $route->getParam('controller');
				    $action     = $route->getParam('action');
				    $class      = $cfg['controllers']['invokables'][$controller];
				    $crowlerCfg = $cfg['wwsse_html_replace'];

				    /* controller/action specific html modification */
				    if(array_key_exists($class, $crowlerCfg) && array_key_exists($action, $crowlerCfg[$class])) {
					    $content    = $e->getResponse()->getContent();
					    $crowler    = HtmlPageCrawler::create($content);

					    $e->getResponse()->setContent($crowlerCfg[$class][$action]($crowler));
				    }

				    /* global specific html modification */
				    if(array_key_exists('global', $crowlerCfg)) {
					    $content    = $e->getResponse()->getContent();
					    $crowler    = HtmlPageCrawler::create($content);

					    $e->getResponse()->setContent($crowlerCfg['global']($crowler));
				    }
			    }
		    }
	    });
    }
}
