<?php

namespace Stems\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmbeddedImageType extends AbstractType
{
	protected $categories;

	public function __construct($category)
	{
		$this->category = $category;
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('category', 'hidden', array(
			'label'     		=> 'Category',
			'error_bubbling' 	=> true,
			'data' 		        => $this->category,
		));	

		$builder->add('upload', 'file', array(
			'label'     		=> false,
			'error_bubbling' 	=> true,
		));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Stems\MediaBundle\Entity\Image',
	    ));
	}

	public function getName()
	{
		return 'media_embedded_image_type';
	}
}
