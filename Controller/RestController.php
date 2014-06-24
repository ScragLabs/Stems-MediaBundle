<?php

namespace Stems\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Symfony\Component\HttpFoundation\RedirectResponse,
	Symfony\Component\HttpFoundation\JsonResponse,
	Symfony\Component\HttpFoundation\Request;

class RestController extends Controller
{
	/**
	 * Display the media manager pop-up admin dialogue
	 */
	public function dialogueAction()
	{
		$em = $this->getDoctrine()->getManager();
		
		$html = $this->renderView('StemsMediaBundle:Rest:dialogue.html.twig', array(
			'vars'	=> null,
		));

		return new JsonResponse(array(
			'success'   => true,
			'html'		=> $html,
		));
	}

	/**
	 * Upload an image and process sizes
	 */
	public function uploadImageAction()
	{
		$em = $this->getDoctrine()->getManager();

		return new JsonResponse(array(
			'success'   => true,
			'message' 	=> '',
		));
	}

	/**
	 * Parse an image from a url and process sizes
	 */
	public function parseImageAction()
	{
		$em = $this->getDoctrine()->getManager();

		return new JsonResponse(array(
			'success'   => true,
			'message' 	=> '',
		));
	}
}
