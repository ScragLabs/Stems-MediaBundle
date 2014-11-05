<?php

namespace Stems\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Stems\SocialBundle\Service\Sharer;

/** 
 * @ORM\Entity
 * @ORM\Table(name="stm_media_gallery")
 */
class Gallery
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @
     */
    protected $id;

    /** 
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $title;

    /** 
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    protected $excerpt;

    /** 
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /** 
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    protected $slug;

    /** 
     * @ORM\Column(type="integer")
     */
    protected $section;

    /** 
     * @ORM\Column(type="integer")
     */
    protected $author;

    /**
     * @ORM\Column(type="string") 
     */
    protected $status = 'Draft';

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $new = true;

    /** 
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /** 
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $published;

    /** 
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaTitle;

    /** 
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaKeywords;

    /** 
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaDescription;

    public function __construct()
    {
        $this->created = new \DateTime;
        $this->updated = new \DateTime;
        $this->slug = 'gallery-'.$this->created->format('d-m-Y');
    }

    /**
     * Create the social sharer object for this gallery, if no platform is passed we return a default configuration
     *
     * @param  string   $platform   The social media platform to generate the sharer for
     * @return Sharer               The generated sharer
     */
    public function getSharer($platform=null)
    {
        $sharer = new Sharer($platform);

        $sharer->setTitle($this->title.' - '.$this->excerpt);
        $sharer->setText($this->title.' - '.$this->excerpt);
        // @todo complete
        // $sharer->setUrl($this->slug);
        // $sharer->setImage($this->image);
        // $sharer->setTags(array());

        return $sharer;
    }
}