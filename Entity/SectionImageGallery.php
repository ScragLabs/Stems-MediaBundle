<?php

namespace Stems\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Stems\CoreBundle\Definition\SectionInstanceInterface;

/** 
 * @ORM\Entity
 * @ORM\Table(name="stm_media_section_imagegallery")
 */
class SectionImageGallery implements SectionInstanceInterface
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** 
     * @ORM\Column(type="text", nullable=true)
     */
    protected $heading;

    /** 
     * @ORM\Column(type="text", nullable=true)
     */
    protected $caption;

    /** 
     * @ORM\Column(type="text")
     */
    protected $style = 'wall';

    /**
     * @ORM\OneToMany(targetEntity="SectionImageGalleryImage", mappedBy="sectionImageGallery")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $images;

    /**
     * Build the html for rendering in the front end, using any nessary custom code
     */
    public function render($services, $link)
    {
        // Render the twig template
        return $services->getTwig()->render('StemsMediaBundle:Section:imageGallery.html.twig', array(
            'section'   => $this,
            'link'      => $link,
        ));
    }

    /**
     * Build the html for admin editor form
     */
    public function editor($services, $link)
    {
        // Build the section from using the generic builder method
        $form = $services->createSectionForm($link, $this);

        // Render the admin form html
        return $services->getTwig()->render('StemsMediaBundle:Section:imageGalleryForm.html.twig', array(
            'form'      => $form->createView(),
            'link'      => $link,
            'section'   => $this,
        ));
    }

    /**
     * Update the section from posted data
     */
    public function save($services, $parameters, $request, $link)
    {
        // Save the values
        $this->setHeading(stripslashes($parameters['heading']));
        $this->setCaption(stripslashes($parameters['caption']));
        $this->setStyle($parameters['style']);

        // Remove previously attached image images
        $this->clearImages();

        $position = 1;

        // Gather the new image images and append them to the gallery
        foreach ($parameters['images'] as $id) {
            $image = $services->getManager()->getRepository('StemsMediaBundle:SectionImageGalleryImage')->find($id);
            $image->setSectionImageGallery($this);
            $image->setPosition($position);

            $services->getManager()->persist($image);
            $position++;
        }
        
        $services->getManager()->persist($this);        
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set heading
     *
     * @param string $heading
     * @return Section
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;
    
        return $this;
    }

    /**
     * Get heading
     *
     * @return string 
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Set caption
     *
     * @param string $caption
     * @return Section
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    
        return $this;
    }

    /**
     * Get caption
     *
     * @return string 
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set style
     *
     * @param string $style
     * @return Section
     */
    public function setStyle($style)
    {
        $this->style = $style;
    
        return $this;
    }

    /**
     * Get style
     *
     * @return string 
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Add images
     *
     * @param \Stems\MediaBundle\Entity\SectionImageGalleryImage $images
     * @return SectionScrapbook
     */
    public function addimage(\Stems\MediaBundle\Entity\SectionImageGalleryImage $image)
    {
        $this->images[] = $image;
    
        return $this;
    }

    /**
     * Remove images
     *
     * @param \Stems\MediaBundle\Entity\SectionImageGalleryImage $image
     */
    public function removeimage(\Stems\MediaBundle\Entity\SectionImageGalleryImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Remove all images
     */
    public function clearImages()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getimages()
    {
        return $this->images;
    }
}