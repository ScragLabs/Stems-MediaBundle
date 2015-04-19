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

	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('transparentColor', array($this, 'transparentColorFilter')),
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

	/**
	 * Make the background color of an image transparent and save as a png
	 *
	 * @param  integer  $id 	The path of the image to modify
	 * @param  array    $color  Tolerance of the replace, 10 = 1/255 in RGB
	 * @return string  			The path of the converted image
	 */
	public function transparentColorFilter($src, $tolerance = 3200)
	{

		// Hacky
		$webPath = substr($src, strpos($src, '/media/cache'));
		$newSrc  = str_replace('.jpg', '.png', $webPath);

		// Only process if it doesn't already exist
		if (!file_exists($_SERVER['DOCUMENT_ROOT'].$newSrc)) {
			$im = new \Imagick($_SERVER['DOCUMENT_ROOT'].$webPath);
			$im->paintTransparentImage($im->getImagePixelColor(0, 0), 0, $tolerance);
			$im->setImageFormat("png");
			$im->writeImage($_SERVER['DOCUMENT_ROOT'].$newSrc);
		}

		return $newSrc;
	}
}