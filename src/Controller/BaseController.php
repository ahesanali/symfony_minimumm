<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\AuthHelperService;



abstract class BaseController extends AbstractController
{
    protected AuthHelperService $authHelper;

    public function __construct(AuthHelperService $authHelper)
    {
        $this->authHelper = $authHelper;
    }

    protected function template(string $view, array $parameters = [])
    {
        global $container;

        $request = $container->get('request_stack')->getCurrentRequest();

        $templateFile = dirname(__DIR__, 2) . "/templates/{$view}.php";

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Template file not found: {$templateFile}");
        }
        $session = $request->getSession();
        $parameters = array_merge($parameters, ['session' => $session]);
        extract($parameters);
        
        include_once $templateFile;
    }
}
