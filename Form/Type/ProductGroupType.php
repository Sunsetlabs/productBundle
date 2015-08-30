<?php

namespace Sunsetlabs\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductGroupType extends AbstractType
{
	protected $gc;

	public function __construct($gc)
	{
		$this->gc = $gc;
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('price', 'integer', array(
            'label' => 'Precio',
            'required' => false
        ));
        $builder->add('code', 'text', array(
            'label' => 'Codigo',
            'required' => false
        ));
        $builder->add('name', 'text', array(
            'label' => 'Nombre',
            'required' => false
        ));
        $builder->add('description', 'textarea', array(
            'label' => 'Descripción',
            'required' => false
        ));

        $group = $builder->getData();

    	$builder->add('images', 'collection', array(
    			'type' => 'image_type',
    			'allow_add' => true,
    			'allow_delete' => true,
    			'by_reference' => false,
                'label' => 'Imagenes'
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