<?php

namespace QuRedirLogin;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;

class Module implements  BootstrapListenerInterface
{

    public function onBootstrap(EventInterface $e)
    {
        $app   = $e->getApplication();
        $em    = $app->getEventManager();
        $event = $em->getSharedManager();
        $event->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($eve)
        {
            $controller      = $eve->getTarget();
            $app             = $eve->getApplication();
            $result          = $eve->getResult();
            $match           = $eve->getRouteMatch();
            $sm              = $app->getServiceManager();
            $auth            = $sm ->get('zfcuser_auth_service');
            $config          = $sm ->get('config');
            $QuAdmConfig     = $config['QuRedirectLogin'];

            // Get NameSpace
            $controllerClass = get_class($controller);
            $Namespace       = substr($controllerClass, 0, strpos($controllerClass, '\\'));


            if(isset($QuAdmConfig[$Namespace]))
            {

                if(!$auth->hasIdentity())
                {

                    // In ZfcUser login return
                    if($match->getMatchedRouteName() == 'zfcuser/login')
                    {
                        return;
                    }
                    else
                    {
                        // Do nothing if the result is a response object
                        if ($result instanceof Response) { return; }

                        // get url to the zfcuser/login route
                        $router = $eve->getRouter();
                        $options['name'] = 'zfcuser/login';
                        $url = $router->assemble(array(), $options);

                        // Work out where were we trying to get to
                        $options['name'] = $match->getMatchedRouteName();
                        $redirect = $router->assemble($match->getParams(), $options);

                        // set up response to redirect to login page
                        $response = $eve->getResponse();
                        if (!$response)
                        {
                            $response = new HttpResponse();
                            $eve->setResponse($response);
                        }
                        $response->getHeaders()->addHeaderLine('Location', $url . '?redirect=' . $redirect);
                        $response->setStatusCode(302);
                    }

                }


            }
        }, -80);
    }


    public function getAutoloaderConfig()
    {  
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
