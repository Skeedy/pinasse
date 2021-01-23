<?php

namespace App\Form;

use App\Entity\Image;
use App\Form\DataTransformer\ServiceToNumberTransformer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    protected $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tranformer = new ServiceToNumberTransformer($this->em);
        $builder
            ->add('file', FileType::class, [
                'attr' => array('class'=> 'btn btn-light'),
                'label' => false,
                'required' => false])
            ->add('alt', null, [
                'attr' => array('class' => 'form-control'),
                'required' => false,
                'label'=> 'Description de l\'image'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
