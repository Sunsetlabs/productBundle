<?php

namespace Sunsetlabs\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductGroupItemType extends AbstractType
{
	protected $pc;
	protected $fields;

	public function __construct($pc, $fields)
	{
		$this->pc = $pc;
		$this->fields = $fields;
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	foreach($this->fields as $field){
	    		$builder->add($field);
    	}
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