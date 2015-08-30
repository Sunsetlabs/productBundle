<?php

namespace Sunsetlabs\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;

class CategoryType extends AbstractType
{
    protected $em;
    
    function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label' => 'Nombre',
        	'constraints' => array(
        		new NotBlank()
        	)
        ));
        $builder->addEventListener(FormEvents::POST_BIND, array($this, 'postBind'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Category' // TODO: inject Category classname
        ));
    }

    public function postBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        
        if (!$data->getId() or $data->getPosition() === null) {
        	$positionObj = $this->em->getRepository(get_class($data))->findOneBy(array('parent' => $data->getParent()), array('position' => 'DESC'));

        	if ($positionObj) {
        		$position = $positionObj->getPosition();
        	}else{
        		$position = 0;
        	}
            $data->setPosition($position + 1);
            $event->setData($data);
        }

    }

    public function getName()
    {
        return 'category_type';
    }
}