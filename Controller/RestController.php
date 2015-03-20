<?php

namespace Stems\MediaBundle\Controller;

use Stems\CoreBundle\Controller\BaseRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Stems\MediaBundle\Entity\Image;

class RestController extends BaseRestController
{
	/**
	 * Handle and set a feature image upload
	 *
	 * @Route("/admin/rest/media/set-feature-image/{category}", name="stems_media_rest_setfeatureimage")
	 */
	public function setFeatureImageAction(Request $request, $category)
	{
		$image = new Image();
		$image->setCategory($category);

		// Build the form 
		$form = $this->createForm('media_image_type', $image);

		if ($form->bind($request)->isValid()) {

			// Upload the file and save the entity
			$em = $this->getDoctrine()->getEntityManager();

			$image->doUpload();
			$em->persist($image);
			$em->flush();

			$meta = array('id' => $image->getId());

			// Get the html for updating the feature image
			$html = $this->renderView('StemsMediaBundle:Rest:setFeatureImage.html.twig', array(
				'image'	=> $image,
			));

			return $this->addHtml($html)->setCallback('updateFeatureImage')->addMeta($meta)->success('Image updated.')->sendResponse();
		} else {
			return $this->error('Please choose an image to upload.', true)->sendResponse();
		}
	}
}
