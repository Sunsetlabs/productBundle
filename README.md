### Instalacion  

Usando Composer:
````js
#composer.json

"require": {
    #other requirments
    "sunsetlabs/product-bundle" : "dev-catalog"
}
````  

Registrar el plugin en el kernel de sf2:
````php
//app/AppKernel.php

$bundles = array(
    // symfony2 bundles
    new Sunsetlabs\ProductBundle\SunsetlabsProductBundle(),
	// any other bundle
````

Registrar las rutas del plugin:
````yml
#app/config/routing.yml  

sunsetlabs_product_bundle_annotation:
    resource: "@SunsetlabsProductBundle/Controller"
    type:     annotation
````
##
### Uso

El plugin provee 3 clases abstractas ha extender:  
- ProductGroup.php
- Product.php
- Category.php

Estas son clases php puras, no tienen ningun tipo de anotacion.
Por lo tanto al extenderlas se deberan agregar las anotacions
necesarias para que doctrine las interprete.  
Si algun campo provisto por la clase no es de interes para el proyecto, simplemente no lo incluyas en la clase padre. De la misma manera, si se quiere agregar algun campo extra, agregarlo a la clase padre con sus respectivos getters y setters.


Las clases padre con los campos dados pueden ser de la forma:


````php
// AppBundle\Entity\ProductGroup.php
namespace AppBundle\Entity;

use Sunsetlabs\ProductBundle\Entity\ProductGroup as BaseProductGroup;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @UniqueEntity("code")
 */
class ProductGroup extends BaseProductGroup
{
   	/**
	 * {@inheritDoc}
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * {@inheritDoc}
     *
     * @ORM\Column(type="integer")
	 */
	protected $price;

	/**
	 * {@inheritDoc}
	 * 
	 * @ORM\Column(type="string", length=200)
	 */
	protected $code;

	/**
	 * {@inheritDoc}
	 * 
	 * @ORM\Column(type="string", length=200)
	 */
	protected $name;

	/**
	 * {@inheritDoc}
	 * 
	 * @ORM\Column(type="text")
	 */
	protected $description;

	/**
	 * {@inheritDoc}
	 * @ORM\ManyToMany(targetEntity="Sunsetlabs\MediaBundle\Entity\Image", cascade={"persist"} )
     * @ORM\JoinTable(name="product_group_images",
     *      joinColumns={@ORM\JoinColumn(name="product_group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)}
     *      )
	 */
	protected $images;

	/**
	 * {@inheritDoc}
	 *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="group", cascade={"persist"}, orphanRemoval=true)
	 */
	protected $products;

	/**
	 * {@inheritDoc}
	 * @ORM\ManyToMany(targetEntity="Category", inversedBy="products")
     * @ORM\JoinTable(name="product_group_category")
	 */
	protected $categories;
   
}

// AppBundle\Entity\Product.php
namespace AppBundle\Entity;

use Sunsetlabs\ProductBundle\Entity\Product as BaseProduct;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Product extends BaseProduct
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="ProductGroup", inversedBy="products")
     * @ORM\JoinColumn(name="product_group_id", referencedColumnName="id")
     */
	protected $group;
    /**
	 * @ORM\Column(type="integer")
	 */
	protected $stock;
    /**
     * @ORM\ManyToMany(targetEntity="Sunsetlabs\MediaBundle\Entity\Image", cascade={"persist"} )
     * @ORM\JoinTable(name="product_images",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")}
     *      )
     **/
	protected $images;
}

// AppBundle\Entity\Category.php
namespace AppBundle\Entity;

use Sunsetlabs\ProductBundle\Entity\Category as BaseCategory;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Category extends BaseCategory
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	protected $id;
    
	/**
	 * @ORM\Column(type="string", length=100)
	 */
	protected $name;
    
	/**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_category_id", referencedColumnName="id", onDelete="CASCADE")
     */
	protected $parent;
	/**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @ORM\OrderBy({ "position" = "ASC" })
     */
	protected $children;
    
	/**
     * @ORM\ManyToMany(targetEntity="ProductGroup", mappedBy="categories")
     **/
	protected $products;
    
	/**
	 * @ORM\Column(type="integer")
	 */
	protected $position = 0;
}
````

##
### Integracion con el backend <small>(*easyadmin*)</small>

Para integrar el catalogo de productos con easyadmin debemos seguir dos pasos:  
- Registrar las entidades en la configuracion de easyadmin
- Agregar configuracion extra en el controlador de admin para el listado de las categorias y que el admin use los formularios provistos por el plugin.

A continuacion se muestra como:

__Registrar entidades__

````yml
# app/config/config.yml  
# Bajo la configuracion de easyadmin
 entities:
        Product:
            label: 'Productos'
            class: AppBundle\Entity\ProductGroup
            list:
                title: Productos
                fields: [ 'id', { label: 'Nombre', property: 'name' }, { label: 'Codigo', property: 'code' }, { label: 'Precio', property: 'price' }]
            templates:
                edit: @SunsetlabsProduct/Group/crud.html.twig
                new:  @SunsetlabsProduct/Group/crud.html.twig
                form: @SunsetlabsProduct/Group/crud.html.twig
        Category:
            class: AppBundle\Entity\Category
            label: 'Categorias'
            list:
                title: 'Categorias'
            new:
                title: 'Crear Categoria'
            templates:
                list: @SunsetlabsProduct/Category/list.html.twig
                form: @SunsetlabsProduct/Category/form.html.twig
````

__Configurar controlador__
````php
// AppBundle\Controller\AdminController.php
// Agregar los siguientes metodos 

protected function createEntityForm($entity, array $entityProperties, $view)
    {
        if (get_class($entity) == 'AppBundle\Entity\ProductGroup') {
            return $this->createForm('product_group_type', $entity, array(
                'attr' => array('class' => 'theme-bootstrap_3_horizontal_layout  form-horizontal')
            ));
        }

        if (get_class($entity) == 'AppBundle\Entity\Category') {

            if ($this->request->query->get('parent', false)) {
                $parent = $this->getDoctrine()->getRepository('AppBundle\Entity\Category')->find($this->request->query->get('parent'));
                $entity->setParent($parent);
            }

            return $this->createForm('category_type', $entity, array(
                'attr' => array('class' => 'theme-bootstrap_3_horizontal_layout  form-horizontal')
            ));
        }
        
        return parent::createEntityForm($entity, $entityProperties, $view);
    }

// Provee al la vista del listado de las categorias padre.
protected function listCategoryAction() 
{
    $categories = $this->getDoctrine()->getRepository($this->entity['class'])->findBy(array('parent' => null), array('position' => 'ASC'));
    return $this->render($this->entity['templates']['list'], array(
            'fields'    => $fields = $this->entity['list']['fields'],
            'categories' => $categories
        ));
    }
````