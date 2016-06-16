<?php

namespace Stems\MediaBundle\Form;

use Symfony\Component\Form\AbstractType,
	Symfony\Component\Form\FormBuilderInterface,
	Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
	protected $categories;

	public function __construct($categories = array('General' => 'general')) 
	{
		$this->categories = $categories;
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('title', null, [
			'label'  			=> 'Title (Optional)',
			'error_bubbling' 	=> true,
		]);

	    $builder->add('accreditation', null, [
		    'label'  			=> 'Accreditation (Optional)',
		    'error_bubbling' 	=> true,
	    ]);

		$builder->add('category', 'choice', [
			'label'     		=> 'Category',
			'error_bubbling' 	=> true,
			'empty_value' 		=> false,
			'choices'			=> $this->categories,
		]);

		$builder->add('upload', null, [
			'label'     		=> 'Upload Image',
			'error_bubbling' 	=> true,
		]);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults([
	        'data_class' => 'Stems\MediaBundle\Entity\Image',
	    ]);
	}

	public function getName()
	{
		return 'media_image_type';
	}
}
