<?php

namespace Sunsetlabs\ProductBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
				'errors' =>$form->getErrorsAsString(),
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
	
	/**
	 * Promote Category
	 *
	 * @Route("/admin/category/promote/{id}", name="promote_cat")
	 * 
	 * @param  Request $request
	 * @param  integer $id  Category Id
	 * @return RedirectResponse
	 */
	public function promoteCategoryAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$repo = $em->getRepository('AppBundle:Category');
		$cat = $repo->find($id);

		$position = $cat->getPosition();

		$switch_cat = $repo->findOneBy(array('parent' => $cat->getParent(), 'position' => $position + 1));

		if ($switch_cat and $switch_cat->getParent() == $cat->getParent()) {
			$cat->setPosition($switch_cat->getPosition());
			$switch_cat->setPosition($position);
			$em->persist($cat);
			$em->persist($switch_cat);
		}else{
			$cat->setPosition($position + 1);
			$em->persist($cat);
		}

		$em->flush();

		return new RedirectResponse($this->generateUrl('admin', array(
			'action' => 'list',
			'entity' => 'Category'
		)));
	}
	
	/**
	 * Demote Category
	 * 
	 * @Route("/admin/category/demote/{id}", name="demote_cat")
	 * 
	 * @param  Request $request
	 * @param  integer  $id Category Id
	 * @return RediractResponse
	 */
	public function demoteCategoryAction(Request $request, $id)
	{	
		$em = $this->getDoctrine()->getEntityManager();
		$repo = $em->getRepository('AppBundle:Category');
		$cat = $repo->find($id);

		$position = $cat->getPosition();

		if ($position > 0) {
			$switch_cat = $repo->findOneBy(array('parent' => $cat->getParent(), 'position' => $position - 1));

			if ($switch_cat and $switch_cat->getParent() == $cat->getParent()) {
				$cat->setPosition($switch_cat->getPosition());
				$switch_cat->setPosition($position);
				$em->persist($cat);
				$em->persist($switch_cat);
			}else{
				$cat->setPosition($position - 1);
				$em->persist($cat);
			}

			$em->flush();
		} 


		return new RedirectResponse($this->generateUrl('admin', array(
			'action' => 'list',
			'entity' => 'Category'
		)));
	}

	/**
	 * Renders Category form widget
	 * 
	 * @Route("/category/widget/{name}/{product_id}", name="category_widget")
	 *
	 * @param  Request $request
	 * @param  string  $name  Form Checkbox name
	 * @param  integer $product_id Product Id
	 */
	public function categoryWidgetRenderAction(Request $request, $name, $product_id)
	{
	    $em = $this->getDoctrine()->getEntityManager();
	    $categories = $em->getRepository('AppBundle:Category')->findRoots(); // TODO: make Category classname a parameter
	    $product = $em->getRepository($this->getParameter('sl.product.group.class'))->find($product_id);

	    return $this->render('@SunsetlabsProduct/Category/widget.html.twig', array('categories' => $categories, 'product' => $product, 'name' => $name));
	}

	/**
	 * Changes image identified by imageId position to the given position and sorts others images
	 * of the same group accordingly
	 *
	 * @Route("/admin/productGroup/images/order/{imageId}/{position}", name="order_product_images")
	 * 
	 * @param  int $imageId
	 * @param  int $position
	 * @return JsonRepsonse
	 */
	public function positionProductAction($imageId, $position)
	{
		try {
			$em = $this->getDoctrine()->getManager();
			$image = $em->getRepository('SunsetlabsMediaBundle:Image')->find($imageId);
			$images = $em->getRepository($this->getParameter('sl.product.group.class'))->find($image->getObjId())->getImages();
			
			$this->reOrderImages($image, $images, $position);
			$em->flush();

			$image->setPosition($position);
			$em->persist($image);
			$em->flush();	

			return new JsonResponse(array(
				'image' => $imageId,
				'position' => $position
			));

		} catch (\Exception $e) {
			return new JsonResponse(array(
				'message' => $e->getMessage()
			), 400);
		}
	}

	/**
	 * Borra la imagen asociada a un grupo de productos.
	 *
	 * @Route("/admin/delete-image/{productId}/{imageId}", name="delete_image")
	 * 
	 * @param  int $productId Identifica al grupo de productos
	 * @param  int $imageId   Identifica a la imagen
	 * @return JsonResponse
	 */
	public function deleteProductGroupImage($productId, $imageId)
	{
	    $em = $this->getDoctrine()->getManager();
	    $product = $em->getRepository($this->getParameter('sl.product.group.class'))->find($productId);
	    $image   = $em->getRepository('SunsetlabsMediaBundle:Image')->find($imageId);

	    try {
	        if ($product and $image) {
	        	$this->reOrderImages($image, $product->getImages(), count($product->getImages()));
	            $product->removeImage($image);
	            $em->persist($product);
	            $em->flush();

	            return new JsonResponse(array(
	                'message' => 'Se ha eliminado la imagen correctamente!'
	            ));
	        }else{
	            $message = "No se ha enctrado el product o imagen.";
	        }
	    } catch (\Exception $e) {
	        $message = $e->getMessage();
	    }

	    return new JsonResponse(array(
	        'message' => $message
	    ), 400);
	}

	/**
	 * Re ordena las imagenes de un grupo.
	 * 
	 * @param  Image $image Imagen que se reposicionara
	 * @param  ArrayCollection $images Imagenes del grupo
	 * @param  int $position Posicion a la que se movera la imagen
	 */
	protected function reOrderImages($image, $images, $position)
	{
		$em = $this->getDoctrine()->getManager();
		$currentPosition = $image->getPosition();

		if ($currentPosition < $position) {
			foreach ($images as $img) {
				if ($img->getPosition() <= $position and $img->getPosition() > $currentPosition) {
					$img->setPosition($img->getPosition() - 1);
					$em->persist($img);
				}
			}
		}elseif($currentPosition > $position) {
			foreach ($images as $img) {
				if ($img->getPosition() >= $position and $img->getPosition() < $currentPosition) {
					$img->setPosition($img->getPosition() + 1);
					$em->persist($img);
				}
			}
		}

		$image->setPosition($position);
		$em->persist($image);
	}
}