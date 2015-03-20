<?php

namespace Stems\MediaBundle\Controller;

use Stems\CoreBundle\Controller\BaseRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Stems\MediaBundle\Entity\Image;

class PopupController extends BaseRestController
{
	/**
	 * Build a popup to set a feature image
	 *
	 * @Route("/admin/popup/media/set-feature-image/{category}", name="stems_media_popup_setfeatureimage")
	 */
	public function setFeatureImageAction(Request $request, $category)
	{
		$image = new Image();
		$image->setCategory($category);

		// Build the form 
		$form = $this->createForm('media_image_type', $image);

		// Get the html for the popup
		$html = $this->renderView('StemsMediaBundle:Popup:setFeatureImage.html.twig', array(
			'existing'	=> rawurldecode($request->query->get('existing')),
			'title'		=> $request->query->get('existing', false) ? 'Change Feature Image' : 'Add Feature Image',
			'form'		=> $form->createView(),
		));

		return $this->addHtml($html)->success('The popup was successfully created.')->sendResponse();
	}
}