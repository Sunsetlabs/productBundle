<?php

namespace Sunsetlabs\ProductBundle\Entity;

use Sunsetlabs\EcommerceResourceBundle\Interfaces\Product\ProductGroupInterface;
use Sunsetlabs\EcommerceResourceBundle\Interfaces\Product\ProductInterface;
use Sunsetlabs\MediaBundle\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;


abstract class ProductGroup implements ProductGroupInterface
{
	protected $id;
	protected $images;
	protected $products;

	public function __construct()
	{
		$this->images = new ArrayCollection();
		$this->products = new ArrayCollection();
	}
	public function getId()
	{
		return $this->id;
	}
	public function getImages()
	{
		return $this->images;
	}
	public function addImage(Image $image)
	{
		$this->images->add($image);
		return $this;
	}
	public function removeImage(Image $image)
	{
		$this->images->removeElement($image);
		return $this;
	}
	public function getProducts()
	{
		return $this->products;
	}
	public function addProduct(ProductInterface $product)
	{
		$this->products->add($product);
		$product->setGroup($this);
		return $this;
	}
	public function removeProduct(ProductInterface $product)
	{
		$this->products->removeElement($product);
		$product->setGroup();
		return $this;
	}
}
