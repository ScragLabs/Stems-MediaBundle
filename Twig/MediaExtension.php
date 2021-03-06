<?php

namespace Stems\MediaBundle\Twig;

use Doctrine\ORM\EntityManager;
use Stems\MediaBundle\Entity\Image;

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
			new \Twig_SimpleFunction('getImageFromUrl', array($this, 'getImageFromUrlFunction'))
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
	 * Gets an image from a url and saves it locally
	 *
	 * @param  string       $src 	    The path of the image to modify
	 * @return Image  			        The image entity
	 */
	public function getImageFromUrlFunction($src)
	{
		// Use the filename to see if we've already downloaded it
		$filename = explode('/', $src);
		$filename = end($filename);
		$image = $this->em->getRepository('StemsMediaBundle:Image')->findOneBy(array('filename' => $filename, 'category' => 'download'));

		// Return image if it already exists
		if (is_object($image)) {
			return $image;
		}

		// Download and save via curl if it doesn't
		$image = new Image();
		$image->doDownload($src);
		$this->em->persist($image);
		$this->em->flush();

		return $image;
	}

	/**
	 * Make the background color of an image transparent and save as a png
	 *
	 * @param  string       $src 	        The path of the image to modify
	 * @param  integer      $tolerance      Tolerance of the replace, 10 = 1/255 in RGB
	 * @return string  		    	        The path of the converted image
	 */
	public function transparentColorFilter($src, $tolerance = 1600)
	{
		// This needs handling better
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