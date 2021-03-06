<?php
namespace Oforge\Engine\Modules\AdminBackend\Core\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
/**
 * @ORM\Table(name="oforge_backend_navigation")
 * @ORM\Entity
 */
class BackendNavigation extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="path", type="string", nullable=true)
     */
    private $path = null;

    /**
     * @var string
     * @ORM\Column(name="icon", type="string", nullable=true)
     */
    private $icon = null;

    /**
     * @var string
     * @ORM\Column(name="parent", type="string", nullable=true)
     */
    private $parent = 0;

    /**
     * @var int
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $order = 1;

    /**
     * @var string
     * @ORM\Column(name="i18n_name", type="string", nullable=false, unique=true)
     */
    private $name = null;

    /**
     * @var string
     * @ORM\Column(name="i18n_title", type="string", nullable=true)
     */
    private $title = null;

    /**
     * @var bool
     * @ORM\Column(name="visible", type="boolean", options={"default":true})
     */
    private $visible = true;

    /**
     * @var string
     * @ORM\Column(name="position", type="string")
     */
    private $position;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return BackendNavigation
     */
    public function setPath(string $path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return BackendNavigation
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     *
     * @return BackendNavigation
     */
    public function setOrder(int $order)
    {
        $this->order = $order;
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
     * @return BackendNavigation
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return BackendNavigation
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @param string $parent
     *
     * @return BackendNavigation
     */
    public function setParent(string $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     *
     * @return BackendNavigation
     */
    public function setVisible(bool $visible)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosition() : string {
        return $this->position;
    }

    /**
     * @param string $position
     *
     * @return BackendNavigation
     */
    public function setPosition(string $position) : BackendNavigation {
        $this->position = $position;
        return $this;
    }
}
