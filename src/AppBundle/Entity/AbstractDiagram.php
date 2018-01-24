<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class AbstractDiagram
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $xmiFile;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getXmiFile()
    {
        return $this->xmiFile;
    }

    /**
     * @param string $xmiFile
     */
    public function setXmiFile($xmiFile)
    {
        $this->xmiFile = $xmiFile;
    }
}
