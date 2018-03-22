<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="configuration")
 */
class Configuration
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     */
    private $language;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $metas = [];

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    /**
     * @return mixed
     */
    public function getMetas(): array
    {
        return $this->metas;
    }

    /**
     * @param array $metas
     */
    public function setMetas(array $metas)
    {
        $this->metas = $metas;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addMeta(string $key, $value)
    {
        $this->metas[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function removeMeta(string $key)
    {
        unset($this->metas[$key]);
    }
}
