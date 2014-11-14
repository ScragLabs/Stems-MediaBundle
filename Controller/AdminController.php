<?php

namespace Stems\MediaBundle\Controller;

use Stems\CoreBundle\Controller\BaseAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\NoResultException;
use Stems\MediaBundle\Entity\Gallery;
use Stems\MediaBundle\Entity\SectionImageGallery;

class AdminController extends BaseAdminController
{
	/**
	 * Render the dialogue for the module's dashboard entry in the admin panel
	 */
	public function dashboardAction()
	{
		return $this->render('StemsMediaBundle:Admin:dashboard.html.twig');
	}

	/**
	 * Overview of all available media
	 */
	public function indexAction()
	{		
		return null;
	}

	/**
	 * Galleries overview
	 */
	public function galleriesAction()
	{		
		// Get all undeleted galleries
		$galleries = $this->em->getRepository('StemsMediaBundle:Gallery')->findBy(array('deleted' => false), array('created' => 'DESC'));

		return $this->render('StemsMediaBundle:Admin:galleries.html.twig', array(
			'galleries' 	=> $galleries,
		));
	}

	/**
	 * Create a new gallery then forward to the edit form
	 *
	 * @param  Request  $request 	The request object
	 */
	public function createGalleryAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();

		// Create a new gallery for persisting
		$gallery = new Gallery();
		$gallery->setAuthor($this->getUser()->getId());
		
		// If a title was posted then use it
		$request->get('title') and $gallery->setTitle($request->get('title'));

		$em->persist($gallery);
		$em->flush();

		// Redirect to the edit page for the new gallery
		return $this->redirect($this->generateUrl('stems_admin_media_galleries_edit', array('id' => $gallery->getId())));
	}

	/**
	 * Edit a gallery
	 *
	 * @param  integer 	$id  		The ID of the gallery
	 * @param  Request  $request 	The request object
	 */
	public function editGalleryAction(Request $request, $id)
	{
		// Get the gallery requested
		$gallery = $this->em->getRepository('StemsMediaBundle:Gallery')->findOneBy(array('id' => $id, 'deleted' => false));

		// Throw an error if the gallery could not be found
		if (!$gallery) {
			$request->getSession()->getFlashBag()->set('error', 'The requested gallery could not be found.');
			return $this->redirect($this->generateUrl('stems_admin_media_galleries'));
		}

		// Create the edit form for the gallery and it's gallery section
		$form = $this->createForm('media_gallery', $gallery);
		$sectionForm = $sectionHandler->getEditors($gallery->getSections());

		// Handle the form submission
		if ($request->getMethod() == 'POST') {

			// Validate the submitted values
			$form->bind($request);

			//if ($form->isValid()) {

				// Update the gallery in the database
				$gallery->setNew(false);
				$gallery->setTitle(stripslashes($gallery->getTitle()));
				$gallery->setExcerpt(stripslashes($gallery->getExcerpt()));
				$gallery->setContent(stripslashes($gallery->getContent()));
				$gallery->setAuthor($this->getUser()->getId());

				// Order the sections, attached to the page and save their values
				$position = 1;

				foreach ($request->get('sections') as $section) {
					
					// Attach and update order
					$sectionEntity = $em->getRepository('StemsBlogBundle:Section')->find($section);
					$sectionEntity->setPost($gallery);
					$sectionEntity->setPosition($position);

					// Get all form fields relevant to the section...
					foreach ($request->request->all() as $parameter => $value) {
						// Strip the section id from the parameter group and save if it matches
						$explode = explode('_', $parameter);
						$parameterId = reset($explode);
						$parameterId == $sectionEntity->getId() and $sectionParameters = $value;
					}

					// ...then process and update the entity
					$sectionHandler->saveSection($sectionEntity, $sectionParameters, $request);
					$em->persist($sectionEntity);

					$position++;
				}

				// If there were no errors then save the entity, otherwise display the save errors
				// if ($sectionHandler->getSaveErrors()) {
					
					$em->persist($gallery);
					$em->flush();
					$request->getSession()->getFlashBag()->set('success', 'The gallery "'.$gallery->getTitle().'" has been updated.');

					return $this->redirect($this->generateUrl('stems_admin_media_galleries'));

				// } else {
				// 	$request->getSession()->getFlashBag()->set('error', 'Your request was not processed as errors were found.');
				// 	$request->getSession()->getFlashBag()->set('debug', '');
				// }
			//}
		}

		return $this->render('StemsBlogBundle:Admin:edit.html.twig', array(
			'form'			=> $form->createView(),
			'sectionForm'	=> $sectionForm->createView(),
			'gallery' 		=> $gallery,
		));
	}

	/**
	 * Delete a gallery
	 *
	 * @param  integer 	$id  		The ID of the gallery
	 * @param  Request  $request 	The request object
	 */
	public function deleteGalleryAction(Request $request, $id)
	{
		// Get the gallery
		$gallery = $this->em->getRepository('StemsMediaBundle:Gallery')->findOneBy(array('id' => $id, 'deleted' => false));

		if ($gallery) {

			// Delete the gallery if was found, saving the name or ID for the flash message
			$name = $gallery->getTitle();
			$name or $name = $gallery->getId();
			$gallery->setDeleted(true);
			$em->persist($gallery);
			$em->flush();

			// Return the success message
			$request->getSession()->getFlashBag()->set('success', 'The gallery "'.$name.'" was successfully deleted!');

		} else {
			$request->getSession()->getFlashBag()->set('error', 'The requested gallery could not be deleted as it does not exist in the database.');
		}

		return $this->redirect($this->generateUrl('stems_admin_media_galleries'));
	}

	/**
	 * Publish/unpublish a gallery
	 *
	 * @param  integer 	$id  		The ID of the gallery
	 * @param  Request  $request 	The request object
	 */
	public function publishGalleryAction(Request $request, $id)
	{
		// Get the gallery
		$gallery = $this->em->getRepository('StemsMediaBundle:Gallery')->findOneBy(array('id' => $id, 'deleted' => false));

		if ($gallery) {

			// Set the gallery to published/unpublished 
			if ($gallery->getStatus() == 'Draft') {	
				$gallery->setStatus('Published');
				$gallery->setPublished(new \DateTime());
				$request->getSession()->getFlashBag()->set('success', 'The gallery "'.$gallery->getTitle().'" was successfully published!');
			} else {
				$gallery->setStatus('Draft');
				$request->getSession()->getFlashBag()->set('success', 'The gallery "'.$gallery->getTitle().'" was successfully unpublished!');
			}

			$em->persist($gallery);
			$em->flush();

		} else {
			$request->getSession()->getFlashBag()->set('error', 'The requested gallery could not be published as it does not exist in the database.');
		}

		return $this->redirect($this->generateUrl('stems_admin_media_galleries'));
	}
}
