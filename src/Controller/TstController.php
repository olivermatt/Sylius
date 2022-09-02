<?php

namespace Acme\SyliusExamplePlugin\Controller;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseProductController;
use Sylius\Component\Resource\ResourceActions;


class TstController extends BaseProductController
{
    public function showAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::SHOW);
        $product = $this->findOr404($configuration);

        $recommendationServiceApi = $this->get('app.recommendation_service_api');

        $recommendedProducts = $recommendationServiceApi->getRecommendedProducts($product);

        $this->eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $product);

        $view = View::create($product);

        if ($configuration->isHtmlRequest()) {
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::SHOW . '.html'))
                ->setTemplateVar($this->metadata->getName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resource' => $product,
                    'recommendedProducts' => $recommendedProducts,
                    $this->metadata->getName() => $product,
                ])
            ;
        }

        return $this->viewHandler->handle($configuration, $view);
    }
}