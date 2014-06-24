<?php

namespace Stems\MediaBundle\Controller;

use Stems\CoreBundle\Controller\BaseAdminController;

class AdminController extends BaseAdminController
{
    public function overviewAction()
    {
        return $this->render('StemsMediaBundle:Default:index.html.twig', array(
        	'name' => $name
        ));
    }
}
