<?php

namespace Sunsetlabs\ProductBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends Controller
{
	/** 
	 * Renders and validates a form to add a product Item to a product Group.
	 * If form is valid returns the rendered row of the item and its id, e.g { row: '', id: '' }.
	 * if form is invalid returns a renderd form with its errors, e.g { html: '' }.
	 * if form is not submitted return a rendered modal of the new item form.
	 *
	 * @Route("/admin/product/add-item/{group}/{item}", name="admin_add_product_item")
	 * 
	 * @param Request $request
	 * @param integer $group Product Group identifier
	 * @param integer|null $item Product Item identifier
	 * @return Response|JsonResponse
	 */
	public function addItemAction(Request $request, $group, $item = null)
	{
		$itemClass = $this->getParameter('sl.product.class');

		$group = $this->getDoctrine()->getRepository($this->getParameter("sl.product.group.class"))->find($group);
		$item = ($item === null)
			? new $itemClass()
			: $this->getDoctrine()->getRepository($itemClass)->find($item);

		$item->setGroup($group);

		$form = $this->createForm('product_group_item_type', $item, array(
			'action' => $this->generateUrl('admin_add_product_item', array(
				'group' => $group->getId(),
				'item'  => $item->getId()
			)),
			'provider' => $group
		));

		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($item);
			$em->flush();

			return new JsonResponse(array(
				'row'  => $this->renderView('@SunsetlabsProduct/Group/list_item.html.twig', array('product' => $item)),
				'id' => $item->getId()
			));

		} elseif ($form->isSubmitted()) {
			return new JsonResponse(array(
				'html' => $this->renderView('@SunsetlabsProduct/Group/add_item_body.html.twig', array('form' => $form->createView()))
			));
		}

		return $this->render('@SunsetlabsProduct/Group/add_item.html.twig', array('form' => $form->createView()));
	}

	/**
	 * Removes a product Item form a product Group.
	 *
	 * @Route("/admin/product/remove-item/{group}/{item}", name="admin_remove_product_item")
	 * 
	 * @param  Request $request
	 * @param  integer $group Group identifier
	 * @param  integer $item  Item identifier
	 * @return JsonResponse          
	 */
	public function removeItemAction(Request $request, $group, $item)
	{
		$em = $this->getDoctrine()->getEntityManager();
		
		try {
			$group = $em->getRepository($this->getParameter('sl.product.group.class'))->find($group);
			$item = $em->getRepository($this->getParameter('sl.product.class'))->find($item);

			$group->removeProduct($item);

			$em->persist($group);
			$em->flush();
		} catch (Exception $e) {
			return new JsonResponse(array(
				'ok' => false,
				'message' => $e->getMessage()
			));
		}

		return new JsonResponse(array(
			'ok' => true,
			'message' => 'Se elimino el producto con exito.'
		));
	}
}