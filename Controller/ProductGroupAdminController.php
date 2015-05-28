<?php

namespace Sunsetlabs\ProductBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProductGroupAdminController
{
	protected $em;
	protected $templating;
	protected $formFactory;
	protected $router;
	protected $product_class;
    protected $product_item_fields;

	public function __construct(EntityManager $em, EngineInterface $templating, FormFactoryInterface $formFactory, RouterInterface $router, $product_group_class, $product_item_fields)
	{
		$this->em = $em;
		$this->templating = $templating;
		$this->formFactory = $formFactory;
		$this->router = $router;
		$this->product_group_class = $product_group_class;
        $this->product_item_fields = $product_item_fields;
	}

    public function newAction(Request $request)
    {
    	$prod = $this->getProduct();
    	$form = $this->formFactory->create('product_group_type', $prod);

    	$form->handleRequest($request);

    	if ($form->isValid())
    	{
    		$this->em->persist($prod);
    		$this->em->flush();
    		return new RedirectResponse($this->router->generate('edit_product_group', array('id' => $prod->getId())));
    	}

        return $this->templating->renderResponse('@SunsetlabsProduct/Forms/ProductGroup/product_group_form.html.twig', array('form' => $form->createView(), 'fields' => $this->product_item_fields));
    }

    public function editAction(Request $request, $id = null)
    {   
        if (!$id) {
            $id = $request->query->get('id');
        }
    	$prod = $this->getProduct($id);
		$form = $this->formFactory->create('product_group_type', $prod);

		$form->handleRequest($request);

		if ($form->isValid())
		{
			$this->em->persist($prod);
			$this->em->flush();
			return new RedirectResponse($this->router->generate('edit_product_group', array('id' => $prod->getId())));
		}

	    return $this->templating->renderResponse('@SunsetlabsProduct/Forms/ProductGroup/product_group_form.html.twig', array('form' => $form->createView(), 'fields' => $this->product_item_fields));
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
            'entity' => 'ProductGroup',
            'view'   => 'list'
        )));
    }

    protected function getProductRepository()
    {
    	return $this->em->getRepository($this->product_group_class);
    }

    protected function getProduct($id = null)
    {
    	if (!$id) {
    		return new $this->product_group_class();
    	}
    	return $this->getProductRepository()->find($id);
    }
}
