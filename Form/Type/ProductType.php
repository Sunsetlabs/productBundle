<?php

namespace Sunsetlabs\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType
{
    protected $pc;
    protected $fields;

    public function __construct($pc, $fields)
    {
        $this->pc =$pc;
        $this->fields = $fields;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($this->fields as $field){
            $builder->add($field);
        }
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
            'data_class' => $this->pc,
        ));
    }

    public function getName()
    {
        return 'product_type';
    }
}