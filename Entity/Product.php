<?php

namespace Sunsetlabs\ProductBundle\Entity;

use Sunsetlabs\EcommerceResourceBundle\Interfaces\Product\ProductInterface;
use Sunsetlabs\EcommerceResourceBundle\Interfaces\Product\ProductGroupInterface;
use Sunsetlabs\MediaBundle\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;

abstract class Product implements ProductInterface
{
	protected $id;
	protected $group;
	protected $stock;
	protected $images;

	public function __construct()
	{
		$this->images = new ArrayCollection();
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
	public function getGroup()
	{
		return $this->group;
	}
	public function setGroup(ProductGroupInterface $group = null)
	{
		$this->group = $group;
		return $this;
	}
	public function getStock()
	{
		return $this->stock;
	}
	public function setStock($stock)
	{
		$this->stock = $stock;
		return $this;
	}
	public function get($attr)
	{
		$cmlAttr = str_replace(' ', '', ucwords(str_replace('-', ' ', $attr)));
		$methodName = 'get'.$cmlAttr;
		if (method_exists($this, $methodName)){
			return $this->$methodName();
		}elseif (method_exists($this->getGroup(), $methodName)) {
			return $this->getGroup()->$methodName();

		}else{
			throw new \Exception("No attr ".$attr." in Product/ProductGroup", 1);
		}
	}
}