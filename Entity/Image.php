<?php
namespace Stems\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;


/** 
 * @ORM\Entity
 * @ORM\Table(name="stm_media_image")
 */
class Image
{
	/** 
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/** 
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $title;

	/**
	 * @Assert\File(maxSize="10000000")
	 */
	protected $upload;

	/** 
	 * @ORM\Column(type="string")
	 */
	protected $filename;

	/** 
	 * @ORM\Column(type="string")
	 */
	protected $src;

	/** 
	 * @ORM\Column(type="string")
	 */
	protected $category = 'general';

	/**
	 * @ORM\Column(type="string") 
	 */
	protected $mime;

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
	protected $deleted = false;

	/** 
	 * @ORM\Column(type="datetime")
	 */
	protected $created;

	/** 
	 * @ORM\Column(type="datetime")
	 */
	protected $updated;

	public function __construct()
	{
		$this->created = new \DateTime;
		$this->updated = new \DateTime;
	}

	/**
	 * Handle the file upload
	 */
	public function doUpload()
	{
		// If no file upload is present
		if ($this->getUpload() === null || $this->getUpload() === 'undefined') {
			return;
		}

		$this->setMime($this->getUpload()->getMimeType());

		// Tidy up the filename a bit, first take the extension off the end
		$filename  = explode('.', $this->getUpload()->getClientOriginalName());
		$extension = end($filename);
		array_pop($filename);
		$filename  = implode($filename);

		// Replace anything non-alphanumeric with a dash
		$filename = preg_replace('~[^\\pL\d]+~u', '-', $filename);

		// Trim
		$filename = trim($filename, '-');

		// Transliterate
		$filename = iconv('utf-8', 'us-ascii//TRANSLIT', $filename);

		// Lowercase
		$filename = strtolower($filename);

		// Remove unwanted characters
		$filename = preg_replace('~[^-\w]+~', '', $filename);

		// Save the uploaded file
		$this->getUpload()->move($this->getServerRoot(), $filename.'.'.$extension);

		// Store the file information
		$this->setFilename($filename.'.'.$extension);
		$this->setSrc('/'.$this->getWebRoot().'/'.$this->filename);
		$this->setWidth(getimagesize($this->getServerRoot().'/'.$filename.'.'.$extension)[0]);
		$this->setHeight(getimagesize($this->getServerRoot().'/'.$filename.'.'.$extension)[1]);

		// Clean up the upload property as we don't need it anymore
		$this->upload = null;
	}

	/**
	 * Download the file, save and update entity
	 */
	public function doDownload($url)
	{
		// Store the basic file information
		$filename = explode('/', $url);
		$filename = end($filename);

		$this->setFilename($filename);
		$this->setCategory('download');
		$this->setSrc('/'.$this->getWebRoot().'/'.$filename);

		// Perform the download
		ini_set('allow_url_fopen', true);
		$url = strpos($url, 'http:') === 0 ? $url : 'http:'.$url;
		copy($url, $this->getPathname());
		ini_set('allow_url_fopen', false);

		// Update the other image data
		$guesser = MimeTypeGuesser::getInstance();
		$this->setMime($guesser->guess($this->getPathname()));
		$this->setWidth(getimagesize($this->getPathname())[0]);
		$this->setHeight(getimagesize($this->getPathname())[1]);
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
	 * Set title
	 *
	 * @param string $title
	 * @return Image
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	
		return $this;
	}

	/**
	 * Get title
	 *
	 * @return string 
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set filename
	 *
	 * @param string $filename
	 * @return Image
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;
	
		return $this;
	}

	/**
	 * Get filename
	 *
	 * @return string 
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * Set upload
	 *
	 * @param UploadedFile $upload
	 */
	public function setUpload($upload = null)
	{
		$this->upload = $upload;
	}

	/**
	 * Get upload
	 *
	 * @return UploadedFile
	 */
	public function getUpload()
	{
		return $this->upload;
	}

	/**
	 * Get the web root for the image src
	 *
	 * @return string
	 */
	public function getWebRoot()
	{
		return 'media/image/'.$this->category;
	}

	/**
	 * Get the server root for the image
	 *
	 * @return string
	 */
	public function getServerRoot()
	{
		return __DIR__.'/../../../../web/'.$this->getWebRoot();
	}

	/**
	 * Get the full path for the image file
	 */
	public function getPathname()
	{
		return $this->getServerRoot().'/'.$this->filename;
	}

	/**
	 * Set src
	 *
	 * @param string $src
	 * @return Image
	 */
	public function setSrc($src)
	{
		$this->src = $src;
	
		return $this;
	}

	/**
	 * Get src
	 *
	 * @return string 
	 */
	public function getSrc()
	{
		return $this->src;
	}

	/**
	 * Set width
	 *
	 * @param integer $width
	 * @return Image
	 */
	public function setWidth($width)
	{
		$this->width = $width;
	
		return $this;
	}

	/**
	 * Get width
	 *
	 * @return integer 
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Set height
	 *
	 * @param integer $height
	 * @return Image
	 */
	public function setHeight($height)
	{
		$this->height = $height;
	
		return $this;
	}

	/**
	 * Get height
	 *
	 * @return integer 
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Set category
	 *
	 * @param string $category
	 * @return Image
	 */
	public function setCategory($category)
	{
		$this->category = $category;
	
		return $this;
	}

	/**
	 * Get category
	 *
	 * @return string 
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * Set mime
	 *
	 * @param string $mime
	 * @return Image
	 */
	public function setMime($mime)
	{
		$this->mime = $mime;
	
		return $this;
	}

	/**
	 * Get mime
	 *
	 * @return string 
	 */
	public function getMime()
	{
		return $this->mime;
	}

	/**
	 * Set deleted
	 *
	 * @param boolean $deleted
	 * @return Image
	 */
	public function setDeleted($deleted)
	{
		$this->deleted = $deleted;
	
		return $this;
	}

	/**
	 * Get deleted
	 *
	 * @return boolean 
	 */
	public function getDeleted()
	{
		return $this->deleted;
	}

	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return Image
	 */
	public function setCreated($created)
	{
		$this->created = $created;
	
		return $this;
	}

	/**
	 * Get created
	 *
	 * @return \DateTime 
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * Set updated
	 *
	 * @param \DateTime $updated
	 * @return Image
	 */
	public function setUpdated($updated)
	{
		$this->updated = $updated;
	
		return $this;
	}

	/**
	 * Get updated
	 *
	 * @return \DateTime 
	 */
	public function getUpdated()
	{
		return $this->updated;
	}
}