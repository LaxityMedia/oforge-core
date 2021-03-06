<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 16.01.2019
 * Time: 09:26
 */

namespace Oforge\Engine\Modules\CMS\Models\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;

/**
 * @ORM\Table(name="oforge_cms_content")
 * @ORM\Entity
 */
class Content extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var ContentType
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Content\ContentType", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     */
    private $type;
    
    /**
     * @var ContentParent
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Content\ContentParent", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="content_parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * @var string
     * @ORM\Column(name="content_name", type="string", nullable=false)
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="css_class", type="string", nullable=false)
     */
    private $cssClass;
    
    /**
     * @var mixed
     * @ORM\Column(name="content_data", type="object", nullable=true)
     */
    private $data;

    /**
     * Content constructor.
     */
    public function __construct()
    {
        $this->pageContents = new ArrayCollection();
    }
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     *
     * @return Content
     */
    public function setId(int $id): Content
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * @return ContentType
     */
    public function getType(): ?ContentType
    {
        return $this->type;
    }
    
    /**
     * @param ContentType $contentType
     *
     * @return Content
     */
    public function setType(?ContentType $type): Content
    {
        $this->type = $type;
        return $this;
    }
    
    /**
     * @return ContentParent
     */
    public function getParent(): ?ContentParent
    {
        return $this->parent;
    }
    
    /**
     * @param int $parent
     * 
     * @return Content
     */
    public function setParent(?ContentParent $parent): Content
    {
        $this->parent = $parent;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     *
     * @return Content
     */
    public function setName(string $name): Content
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCssClass(): string
    {
        return $this->cssClass;
    }
    
    /**
     * @param string $cssClass
     *
     * @return Content
     */
    public function setCssClass(string $cssClass): Content
    {
        $this->cssClass = $cssClass;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * @param mixed $data
     * 
     * @return Content
     */
    public function setData($data): Content
    {
        $this->data = $data;
        return $this;
    }
}
