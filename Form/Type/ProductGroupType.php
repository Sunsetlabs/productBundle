<?php

namespace Sunsetlabs\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductGroupType extends AbstractType
{
	protected $gc;
	protected $fields;

	public function __construct($gc, $fields)
	{
		$this->gc = $gc;
		$this->fields = $fields;
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	foreach($this->fields as $field){
    		$builder->add($field);
    	}

        $group = $builder->getData();

    	$builder->add('products', 'collection', array(
    			'type' => 'product_group_item_type',
                'options' => array('provider' => $group),
    			'allow_add' => true,
    			'allow_delete' => true,
    			'by_reference' => false
    	));

    	$builder->add('images', 'collection', array(
    			'type' => 'image_type',
    			'allow_add' => true,
    			'allow_delete' => true,
    			'by_reference' => false
    	));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->gc,
        ));
    }

    public function getName()
    {
        return 'product_group_type';
    }
}