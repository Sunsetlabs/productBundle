<?php

namespace Sunsetlabs\ProductBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\HttpKernel;

class ProductAdminController
{
	protected $em;
	protected $templating;
	protected $formFactory;
	protected $router;
    protected $httpKernel;
	protected $product_class;

	public function __construct(EntityManager $em, EngineInterface $templating, FormFactoryInterface $formFactory, RouterInterface $router,HttpKernel $httpKernel, $product_class)
	{
		$this->em = $em;
		$this->templating = $templating;
		$this->formFactory = $formFactory;
		$this->router = $router;
        $this->httpKernel = $httpKernel;
		$this->product_class = $product_class;
	}

    public function newAction(Request $request)
    {
    	$prod = $this->getProduct();
    	$form = $this->formFactory->create('product_type', $prod);

    	$form->handleRequest($request);

    	if ($form->isValid())
    	{
    		$this->em->persist($prod);
    		$this->em->flush();
    		return new RedirectResponse($this->router->generate('admin', array(
                        'action' => 'list',
                        'entity' => 'Product',
                        'view'   => 'list'
                    )));
    	}

        return $this->templating->renderResponse('@SunsetlabsProduct/Forms/Product/product_form.html.twig', array('form' => $form->createView()));
    }

    public function editAction(Request $request, $id = null)
    {   
        if (!$id) {
            $id = $request->query->get('id');
        }

        if ($request->isXmlHttpRequest()) {
            $attributes = array(
                '_controller' => 'AppBundle:Admin:index'
            );
            $query = $request->query->all();
            $query['action'] = 'ajaxEdit';
    
            $subrequest = $request->duplicate($query, null, $attributes);
            return $this->httpKernel->handle($subrequest, HttpKernelInterface::SUB_REQUEST);
        }

    	$prod = $this->getProduct($id);
		$form = $this->formFactory->create('product_type', $prod);

		$form->handleRequest($request);

		if ($form->isValid())
		{
			$this->em->persist($prod);
			$this->em->flush();
			return new RedirectResponse($this->router->generate('admin', array(
                        'action' => 'list',
                        'entity' => 'Product',
                        'view'   => 'list'
                    )));
		}

	    return $this->templating->renderResponse('@SunsetlabsProduct/Forms/Product/product_form.html.twig', array('form' => $form->createView()));
    }

    public function deleteAction(Request $request, $id = null)
    {
        if (!$id) {
            $id = $request->query->get('id');
        }
        $prod = $this->getProduct($id);
        $this->em->remove($prod);
        $this->em->flush();
        return new RedirectResponse($this->router->generate('admin', array(
            'action' => 'list',
            'entity' => 'Product',
            'view'   => 'list'
        )));
    }

    public function retriveProductsAction(Request $request)
    {

        $term = $request->query->get('term', null);
        $products = $this->getProductRepository()->findByTerm($term);

        return new JsonResponse($products);
    }


    public function retriveProductInfoAction(Request $request)
    {
        $id = $request->request->get('id');
        $prod = $this->getProduct($id);

        $attributes = $request->request->get('attributes');

        $result = array();

        foreach ($attributes as $attr) {
            $result[$attr] = $prod->get($attr);
        }

        return new JsonResponse($result);
    }

    protected function getProductRepository()
    {
    	return $this->em->getRepository($this->product_class);
    }

    protected function getProduct($id = null)
    {
    	if (!$id) {
    		return new $this->product_class();
    	}
    	return $this->getProductRepository()->find($id);
    }
}
