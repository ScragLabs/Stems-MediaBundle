<?php

namespace Stems\MediaBundle\Twig;

use Doctrine\ORM\EntityManager;

class MediaExtension extends \Twig_Extension
{
	/**
	 * The entity manager
	 */
	protected $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('getMediaImage', array($this, 'getMediaImageFunction')),
		);
	}

	public function getName()
	{
		return 'stems_media_extension';
	}

	/**
	 * Gets a media image entity based on the id provided
	 *
	 * @param  integer  $id 	The id media image entity
	 * @return Image  			The image entity
	 */
	public function getMediaImageFunction($id)
	{
		// Get the media entity
		$image = $this->em->getRepository('StemsMediaBundle:Image')->find($id);

		// Skip if we can't find the image
		if (!is_object($image)) {
			return null;
		}

		return $image;
	}	
}