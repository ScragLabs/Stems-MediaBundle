<?php
namespace Stems\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;


/** 
 * @ORM\Entity
 * @ORM\Table(name="stm_media_size")
 */
class Size
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** 
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string") 
     */
    protected $slug;

    /** 
     * @ORM\Column(type="integer")
     */
    protected $height;

    /** 
     * @ORM\Column(type="integer")
     */
    protected $width;

    /**
     * @ORM\Column(type="boolean") 
     */
    protected $crop = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;

    /**
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="sizes")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    protected $type;
}