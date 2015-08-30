<?php

namespace Sunsetlabs\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductGroupItemType extends AbstractType
{
	protected $pc;

	public function __construct($pc)
	{
		$this->pc = $pc;
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('stock', 'integer', array(
            'label' => 'Stock',
            'required' => false
        ));

        $builder->add('images', 'entity', array(
            'class' => 'SunsetlabsMediaBundle:Image',
            'choices' => $options['provider']->getImages(),
            'multiple' => true
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->pc,
            'provider'  => null
        ));
    }

    public function getName()
    {
        return 'product_group_item_type';
    }
}