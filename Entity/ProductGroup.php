<?php

namespace Sunsetlabs\ProductBundle\Entity;

use Sunsetlabs\EcommerceResourceBundle\Interfaces\Product\ProductGroupInterface;
use Sunsetlabs\EcommerceResourceBundle\Interfaces\Product\ProductInterface;
use Sunsetlabs\MediaBundle\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ProductGroup Base class 
 * Blueprints for basic product groups
 *
 * @author Alejo Rodriguez <a.rodsott@gmail.com>
 */
abstract class ProductGroup implements ProductGroupInterface
{
	/**
	 * ProductGroup id
	 * @var integer
	 */
	protected $id;

	/**
	 * ProductGroup Price
	 * @var integer
	 */
	protected $price;

	/**
	 * ProductGroup Code
	 * @var string
	 */
	protected $code;

	/**
	 * ProductGroup Name
	 * @var string
	 */
	protected $name;

	/**
	 * ProductGroup Description
	 * @var string
	 */
	protected $description;

	/**
	 * ProductGroup Images
	 * @var ArrayCollection
	 */
	protected $images;

	/**
	 * ProductGroup Items
	 * @var ArrayCollection
	 */
	protected $products;

	/**
	 * ProductGroup Categories
	 * @var ArrayCollection
	 */
	protected $categories;

	public function __construct()
	{
		$this->images = new ArrayCollection();
		$this->products = new ArrayCollection();
		$this->categories = new ArrayCollection();
	}

	/**
	 * Gets id
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Sets Price
	 * @param integer $price
	 * @return ProductGroup
	 */
	public function setPrice($price)
	{
		$this->price = $price;
		return $this;
	}

	/**
	 * Gets Price
	 * @return integer
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * Sets Code
	 * @param string $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * Gets Code
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Sets Name
	 * @param string $name
	 * @return ProductGroup
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Gets Name
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets Description
	 * @param string $description
	 * @return ProductGroup
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * Gets Description
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Gets Images
	 * @return ArrayCollection
	 */
	public function getImages()
	{
		return $this->images;
	}

	/**
	 * Adds Image
	 * @param Image $image
	 * @return ProductGroup
	 */
	public function addImage(Image $image)
	{
		$this->images->add($image);
		return $this;
	}

	/**
	 * Removes Image
	 * @param  Image  $image
	 * @return ProductGroup
	 */
	public function removeImage(Image $image)
	{
		$this->images->removeElement($image);
		return $this;
	}

	/**
	 * Gets Products
	 * @return ArrayCollection
	 */
	public function getProducts()
	{
		return $this->products;
	}

	/**
	 * Adds Product
	 * @param ProductInterface $product
	 * @return ProductGroup
	 */
	public function addProduct(ProductInterface $product)
	{
		$this->products->add($product);
		$product->setGroup($this);
		return $this;
	}

	/**
	 * Removes Product
	 * @param  ProductInterface $product
	 * @return ProductGroup
	 */
	public function removeProduct(ProductInterface $product)
	{
		$this->products->removeElement($product);
		$product->setGroup();
		return $this;
	}

	/**
	 * Gets Categories
	 * @return ArrayCollection
	 */
	public function getCategories()
	{
		return $this->categories;
	}

	/**
	 * Adds Category
	 * @param Category $category
	 * @return ProductGroup
	 */
	public function addCategory($category)
	{
		$category->addProduct($this);
		$this->categories->add($category);
		return $this;
	}

	/**
	 * Removes Category
	 * @return Category
	 */
	public function removeCategory($category)
	{
		$this->categories->removeElement($category);
		return $this;
	}

	/**
	 * Checks if ProductGroup has a Category
	 * @param  Category  $category
	 * @return boolean
	 */
	public function hasCategory($category)
	{
		return $this->categories->exists(function($key, $cat) use ($category) {
			return ($category->getId() == $cat->getId());
		});
	}
}
