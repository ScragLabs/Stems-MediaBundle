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
			new \Twig_SimpleFilter('getMediaImage', array($this, 'getMediaImageFunction')),
		);
	}

	public function getName()
	{
		return 'stems_media_extension';
	}

	/**
	 * Gets the src of the media image file based on the id provided
	 *
	 * @param  integer  $id         The id media image entity
	 * @return string               The image src
	 */
	public function getMediaImageFunction($id)
	{
		// Get the media entity
		$image = $this->em->getRepository('StemsMediaBundle:Image')->find($id);

		// Skip if we can't find the image
		if (!is_object($image)) {
			return null;
		}

		return $image->getSrc();
	}	
}