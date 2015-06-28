<?php

namespace Jam\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use SplFileInfo;
use DateTime;

class Image implements ImageInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     * @Assert\Image(minWidth="5138", minHeight="5596")
     */
    protected $file;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="text")
     */
    protected $path;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
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

    public function hasFile()
    {
        return null !== $this->file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function hasPath()
    {
        return null !== $this->path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        if ( !$this->isExternalImage()){
            return null === $this->path
                ? null
                : $this->getUploadDir().'/'.$this->path;
        }else{
            return null === $this->path
                ? null
                : $this->path;
        }
    }

    public function isExternalImage()
    {
        if ( file_exists($this->getAbsolutePath())){
            return false;
        }else{
            return true;
        }
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            $this->path = '';
            return;
        }

        if (method_exists($this->getFile(), 'getClientOriginalName')){
            $new_file_name = time().'_'.$this->getFile()->getClientOriginalName();
        }else{
            $new_file_name = time().'_.jpg';
        }

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $new_file_name
        );

        // set the path property to the filename where you've saved the file
        $this->path = $new_file_name;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }
}
