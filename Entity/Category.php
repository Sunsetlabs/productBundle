<?php

namespace Sunsetlabs\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Base Category class, blueprint for ecommerce product category
 *
 * @author Alejo Rodriguez <a.rodsott@gmail.com>
 */
abstract class Category
{
	/**
	 * Category id
	 * @var integer
	 */
	protected $id;
	/**
	 * Category Name
	 * @var string
	 */
	protected $name;
	/**
	 * Category Parent
	 * @var Category
	 */
	protected $parent;
	/**
	 * Category Children
	 * @var ArrayCollection
	 */
	protected $children;
	/**
	 * Category Position
	 * @var integer
	 */
	protected $position;
	/**
	 * Category Products
	 * @var ArrayCollection
	 */
	protected $products;

	function __construct() {
		$this->children = new ArrayCollection();
		$this->position = new ArrayCollection();
	}

	/**
	 * Gets Id
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Sets Name
	 * @param string $name
	 * @return Category
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
	 * Checks if Category is root
	 * @return boolean
	 */
	public function isRoot()
	{
		return $this->getParent()
			?false
			:true;
	}

	/**
	 * Gets Category Root parent
	 * @return Category
	 */
	public function getRoot()
	{
		$cat = $this;
		while (!$cat->isRoot()) {
			$cat = $cat->getParent();
		}
		return $cat;
	}

	/**
	 * Sets Category Parent
	 * @param Category|null $parent
	 * @return Category
	 */
	public function setParent($parent = null)
	{
		$this->parent = $parent;
		if ($parent != null){
			$parent->addChildren($this);
		}
		return $this;
	}

	/**
	 * Gets Category Parent
	 * @return Category|null
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Sets Category Position
	 * @param integer $position
	 * @return Category
	 */
	public function setPosition($position)
	{
		$this->position = $position;
		return $this;
	}

	/**
	 * Gets Category Position
	 * @return integer
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Gets Category Children
	 * @return ArrayCollection
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * Adds Children to Category
	 * @param Category $child
	 * @return Category
	 */
	public function addChildren(Category $child)
	{
		if (!$this->children->contains($child)) {
			$this->children->add($child);
			$child->setParent($this);
		}
		return $this;
	}

	/**
	 * Remove Children to Category
	 * @param  Category $child 
	 * @return Category          
	 */
	public function removeChildren(Category $child)
	{
		$this->children->removeElement($child);
		$child->setParent();
		return $this;
	}

	/**
	 * Gets Category Products
	 * @return ArrayCollection
	 */
	public function getProducts()
	{
		return $this->products;
	}

	/**
	 * Adds Product to Category
	 * @param ProductGroup $product
	 * @return Category
	 */
	public function addProduct($product)
	{
		$this->products->add($product);
		return $this;
	}

	/**
	 * Gets Category Slug
	 * @return string
	 */
	public function getSlug()
	{
		$text = $this->name;
		
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);
		$text = trim($text, '-');
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		$text = strtolower($text);
		$text = preg_replace('~[^-\w]+~', '', $text);

		if (empty($text))
		{
		  return 'n-a';
		}

		return $text;
	}

}