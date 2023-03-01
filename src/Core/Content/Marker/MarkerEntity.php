<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Marker;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class MarkerEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected $markerId;
    /**
     * @var MediaEntity|null
     */
    protected $marker;
    /**
     * @var string|null
     */
    protected $markerRetinaId;
    /**
     * @var MediaEntity|null
     */
    protected $markerRetina;
    /**
     * @var string|null
     */
    protected $markerShadowId;
    /**
     * @var MediaEntity|null
     */
    protected $markerShadow;
    /**
     * @var array|null
     */
    protected $markerSettings;
    /**
     * @var null|string
     */
    protected $type;
    /**
     * @var null|string
     */
    protected $name;

    public function getMarkerId(): ?string
    {
        return $this->markerId;
    }

    public function setMarkerId(?string $markerId): void
    {
        $this->markerId = $markerId;
    }

    public function getMarker(): ?MediaEntity
    {
        return $this->marker;
    }

    public function setMarker(?MediaEntity $marker): void
    {
        $this->marker = $marker;
    }

    public function getMarkerRetinaId(): ?string
    {
        return $this->markerRetinaId;
    }

    public function setMarkerRetinaId(?string $markerRetinaId): void
    {
        $this->markerRetinaId = $markerRetinaId;
    }

    public function getMarkerRetina(): ?MediaEntity
    {
        return $this->markerRetina;
    }

    public function setMarkerRetina(?MediaEntity $markerRetina): void
    {
        $this->markerRetina = $markerRetina;
    }

    public function getMarkerShadowId(): ?string
    {
        return $this->markerShadowId;
    }

    public function setMarkerShadowId(?string $markerShadowId): void
    {
        $this->markerShadowId = $markerShadowId;
    }

    public function getMarkerShadow(): ?MediaEntity
    {
        return $this->markerShadow;
    }

    public function setMarkerShadow(?MediaEntity $markerShadow): void
    {
        $this->markerShadow = $markerShadow;
    }

    public function getMarkerSettings(): ?array
    {
        return $this->markerSettings;
    }

    public function setMarkerSettings(?array $markerSettings): void
    {
        $this->markerSettings = $markerSettings;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
