<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock\MerchantStockCollection;
use Moorl\MerchantFinder\Core\Content\OpeningHour\OpeningHourCollection;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\Country\Aggregate\CountryState\CountryStateEntity;
use Shopware\Core\System\Country\CountryEntity;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\Salutation\SalutationEntity;
use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\System\Tag\TagCollection;

class MerchantEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var MerchantStockCollection|null
     */
    protected $merchantStocks;
    /**
     * @var OpeningHourCollection|null
     */
    protected $merchantOpeningHours;
    /**
     * @var string|null
     */
    protected $cmsPageId;
    /**
     * @var CmsPageEntity|null
     */
    protected $cmsPage;
    /**
     * @var string|null
     */
    protected $seoUrl;
    /**
     * @var int|null
     */
    protected $distance;
    /**
     * @var bool|null
     */
    protected $allowMerchantCustomerContact;
    /**
     * @var bool|null
     */
    protected $highlight;
    /**
     * @var int|null
     */
    protected $priority;
    /**
     * @var ProductManufacturerCollection|null
     */
    protected $manufacturers;
    /**
     * @var ProductManufacturerCollection|null
     */
    protected $productManufacturers;
    /**
     * @var TagCollection|null
     */
    protected $tags;
    /**
     * @var CategoryCollection|null
     */
    protected $categories;
    /**
     * @var string|null
     */
    protected $mediaId;
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
    protected $markerShadowId;
    /**
     * @var MediaEntity|null
     */
    protected $markerShadow;
    /**
     * @var string|null
     */
    protected $markerSettings;
    /**
     * @var string|null
     */
    protected $salesChannelId;
    /**
     * @var string|null
     */
    protected $customerGroupId;
    /**
     * @var MediaEntity|null
     */
    protected $media;
    /**
     * @var ProductManufacturerEntity|null
     */
    protected $manufacturer;
    /**
     * @var string|null
     */
    protected $firstName;
    /**
     * @var string|null
     */
    protected $lastName;
    /**
     * @var string|null
     */
    protected $company;
    /**
     * @var string|null
     */
    protected $email;
    /**
     * @var string|null
     */
    protected $title;
    /**
     * @var bool|null
     */
    protected $active;
    /**
     * @var SalesChannelEntity|null
     */
    protected $salesChannel;
    /**
     * @var array|null
     */
    protected $customFields;
    /**
     * @var array|null
     */
    protected $data;
    /**
     * @var SalutationEntity|null
     */
    protected $salutation;
    /**
     * @var string|null
     */
    protected $countryId;
    /**
     * @var string|null
     */
    protected $countryStateId;
    /**
     * @var string|null
     */
    protected $salutationId;
    /**
     * @var string|null
     */
    protected $zipcode;
    /**
     * @var string|null
     */
    protected $city;
    /**
     * @var string|null
     */
    protected $department;
    /**
     * @var string|null
     */
    protected $street;
    /**
     * @var string|null
     */
    protected $vatId;
    /**
     * @var string|null
     */
    protected $phoneNumber;
    /**
     * @var string|null
     */
    protected $additionalAddressLine1;
    /**
     * @var string|null
     */
    protected $additionalAddressLine2;
    /**
     * @var CountryEntity|null
     */
    protected $country;
    /**
     * @var CountryStateEntity|null
     */
    protected $countryState;
    /**
     * @var CustomerEntity|null
     */
    protected $customer;
    /**
     * @var string|null
     */
    protected $shopUrl;
    /**
     * @var string|null
     */
    protected $merchantUrl;
    /**
     * @var string|null
     */
    protected $description;
    /**
     * @var string|null
     */
    protected $openingHours;
    /**
     * @var float|null
     */
    protected $locationLat;
    /**
     * @var float|null
     */
    protected $locationLon;
    /**
     * @var string|null
     */
    protected $originId;
    /**
     * @var string|null
     */
    protected $streetNumber;
    /**
     * @var null|string
     */
    protected $countryCode;

    /**
     * @return MerchantStockCollection|null
     */
    public function getMerchantStocks(): ?MerchantStockCollection
    {
        return $this->merchantStocks;
    }

    /**
     * @param MerchantStockCollection|null $merchantStocks
     */
    public function setMerchantStocks(?MerchantStockCollection $merchantStocks): void
    {
        $this->merchantStocks = $merchantStocks;
    }

    /**
     * @return OpeningHourCollection|null
     */
    public function getMerchantOpeningHours(): ?OpeningHourCollection
    {
        return $this->merchantOpeningHours;
    }

    /**
     * @param OpeningHourCollection|null $merchantOpeningHours
     */
    public function setMerchantOpeningHours(?OpeningHourCollection $merchantOpeningHours): void
    {
        $this->merchantOpeningHours = $merchantOpeningHours;
    }

    /**
     * @return bool|null
     */
    public function getAllowMerchantCustomerContact(): ?bool
    {
        return $this->allowMerchantCustomerContact;
    }

    /**
     * @param bool|null $allowMerchantCustomerContact
     */
    public function setAllowMerchantCustomerContact(?bool $allowMerchantCustomerContact): void
    {
        $this->allowMerchantCustomerContact = $allowMerchantCustomerContact;
    }

    /**
     * @return MediaEntity|null
     */
    public function getMarkerShadow(): ?MediaEntity
    {
        return $this->markerShadow;
    }

    /**
     * @param MediaEntity|null $markerShadow
     */
    public function setMarkerShadow(?MediaEntity $markerShadow): void
    {
        $this->markerShadow = $markerShadow;
    }

    /**
     * @return CmsPageEntity|null
     */
    public function getCmsPage(): ?CmsPageEntity
    {
        return $this->cmsPage;
    }

    /**
     * @param CmsPageEntity|null $cmsPage
     */
    public function setCmsPage(?CmsPageEntity $cmsPage): void
    {
        $this->cmsPage = $cmsPage;
    }

    /**
     * @return string|null
     */
    public function getCmsPageId(): ?string
    {
        return $this->cmsPageId;
    }

    /**
     * @param string|null $cmsPageId
     */
    public function setCmsPageId(?string $cmsPageId): void
    {
        $this->cmsPageId = $cmsPageId;
    }

    /**
     * @return string|null
     */
    public function getSeoUrl(): ?string
    {
        return $this->seoUrl;
    }

    /**
     * @param string|null $seoUrl
     */
    public function setSeoUrl(?string $seoUrl): void
    {
        $this->seoUrl = $seoUrl;
    }

    /**
     * @return int|null
     */
    public function getDistance(): ?int
    {
        return $this->distance;
    }

    /**
     * @param int|null $distance
     */
    public function setDistance(?int $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return bool|null
     */
    public function getHighlight(): ?bool
    {
        return $this->highlight;
    }

    /**
     * @param bool|null $highlight
     */
    public function setHighlight(?bool $highlight): void
    {
        $this->highlight = $highlight;
    }

    /**
     * @return int|null
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * @param int|null $priority
     */
    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return ProductManufacturerCollection|null
     */
    public function getManufacturers(): ?ProductManufacturerCollection
    {
        return $this->manufacturers;
    }

    /**
     * @param ProductManufacturerCollection|null $manufacturers
     */
    public function setManufacturers(?ProductManufacturerCollection $manufacturers): void
    {
        $this->manufacturers = $manufacturers;
    }

    /**
     * @return ProductManufacturerCollection|null
     */
    public function getProductManufacturers(): ?ProductManufacturerCollection
    {
        return $this->productManufacturers;
    }

    /**
     * @param ProductManufacturerCollection|null $productManufacturers
     */
    public function setProductManufacturers(?ProductManufacturerCollection $productManufacturers): void
    {
        $this->productManufacturers = $productManufacturers;
    }

    /**
     * @return TagCollection|null
     */
    public function getTags(): ?TagCollection
    {
        return $this->tags;
    }

    /**
     * @param TagCollection|null $tags
     */
    public function setTags(?TagCollection $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return CategoryCollection|null
     */
    public function getCategories(): ?CategoryCollection
    {
        return $this->categories;
    }

    /**
     * @param CategoryCollection|null $categories
     */
    public function setCategories(?CategoryCollection $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return string|null
     */
    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    /**
     * @param string|null $mediaId
     */
    public function setMediaId(?string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    /**
     * @return string|null
     */
    public function getMarkerId(): ?string
    {
        return $this->markerId;
    }

    /**
     * @param string|null $markerId
     */
    public function setMarkerId(?string $markerId): void
    {
        $this->markerId = $markerId;
    }

    /**
     * @return MediaEntity|null
     */
    public function getMarker(): ?MediaEntity
    {
        return $this->marker;
    }

    /**
     * @param MediaEntity|null $marker
     */
    public function setMarker(?MediaEntity $marker): void
    {
        $this->marker = $marker;
    }

    /**
     * @return string|null
     */
    public function getMarkerShadowId(): ?string
    {
        return $this->markerShadowId;
    }

    /**
     * @param string|null $markerShadowId
     */
    public function setMarkerShadowId(?string $markerShadowId): void
    {
        $this->markerShadowId = $markerShadowId;
    }

    /**
     * @return string|null
     */
    public function getMarkerSettings(): ?string
    {
        return $this->markerSettings;
    }

    /**
     * @param string|null $markerSettings
     */
    public function setMarkerSettings(?string $markerSettings): void
    {
        $this->markerSettings = $markerSettings;
    }

    /**
     * @return string|null
     */
    public function getSalesChannelId(): ?string
    {
        return $this->salesChannelId;
    }

    /**
     * @param string|null $salesChannelId
     */
    public function setSalesChannelId(?string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    /**
     * @return string|null
     */
    public function getCustomerGroupId(): ?string
    {
        return $this->customerGroupId;
    }

    /**
     * @param string|null $customerGroupId
     */
    public function setCustomerGroupId(?string $customerGroupId): void
    {
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return MediaEntity|null
     */
    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    /**
     * @param MediaEntity|null $media
     */
    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }

    /**
     * @return ProductManufacturerEntity|null
     */
    public function getManufacturer(): ?ProductManufacturerEntity
    {
        return $this->manufacturer;
    }

    /**
     * @param ProductManufacturerEntity|null $manufacturer
     */
    public function setManufacturer(?ProductManufacturerEntity $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     */
    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool|null $active
     */
    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return SalesChannelEntity|null
     */
    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }

    /**
     * @param SalesChannelEntity|null $salesChannel
     */
    public function setSalesChannel(?SalesChannelEntity $salesChannel): void
    {
        $this->salesChannel = $salesChannel;
    }

    /**
     * @return array|null
     */
    public function getCustomFields(): ?array
    {
        return $this->customFields;
    }

    /**
     * @param array|null $customFields
     */
    public function setCustomFields(?array $customFields): void
    {
        $this->customFields = $customFields;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     */
    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return SalutationEntity|null
     */
    public function getSalutation(): ?SalutationEntity
    {
        return $this->salutation;
    }

    /**
     * @param SalutationEntity|null $salutation
     */
    public function setSalutation(?SalutationEntity $salutation): void
    {
        $this->salutation = $salutation;
    }

    /**
     * @return string|null
     */
    public function getCountryId(): ?string
    {
        return $this->countryId;
    }

    /**
     * @param string|null $countryId
     */
    public function setCountryId(?string $countryId): void
    {
        $this->countryId = $countryId;
    }

    /**
     * @return string|null
     */
    public function getCountryStateId(): ?string
    {
        return $this->countryStateId;
    }

    /**
     * @param string|null $countryStateId
     */
    public function setCountryStateId(?string $countryStateId): void
    {
        $this->countryStateId = $countryStateId;
    }

    /**
     * @return string|null
     */
    public function getSalutationId(): ?string
    {
        return $this->salutationId;
    }

    /**
     * @param string|null $salutationId
     */
    public function setSalutationId(?string $salutationId): void
    {
        $this->salutationId = $salutationId;
    }

    /**
     * @return string|null
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * @param string|null $zipcode
     */
    public function setZipcode(?string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getDepartment(): ?string
    {
        return $this->department;
    }

    /**
     * @param string|null $department
     */
    public function setDepartment(?string $department): void
    {
        $this->department = $department;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string|null $street
     */
    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string|null
     */
    public function getVatId(): ?string
    {
        return $this->vatId;
    }

    /**
     * @param string|null $vatId
     */
    public function setVatId(?string $vatId): void
    {
        $this->vatId = $vatId;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getAdditionalAddressLine1(): ?string
    {
        return $this->additionalAddressLine1;
    }

    /**
     * @param string|null $additionalAddressLine1
     */
    public function setAdditionalAddressLine1(?string $additionalAddressLine1): void
    {
        $this->additionalAddressLine1 = $additionalAddressLine1;
    }

    /**
     * @return string|null
     */
    public function getAdditionalAddressLine2(): ?string
    {
        return $this->additionalAddressLine2;
    }

    /**
     * @param string|null $additionalAddressLine2
     */
    public function setAdditionalAddressLine2(?string $additionalAddressLine2): void
    {
        $this->additionalAddressLine2 = $additionalAddressLine2;
    }

    /**
     * @return CountryEntity|null
     */
    public function getCountry(): ?CountryEntity
    {
        return $this->country;
    }

    /**
     * @param CountryEntity|null $country
     */
    public function setCountry(?CountryEntity $country): void
    {
        $this->country = $country;
    }

    /**
     * @return CountryStateEntity|null
     */
    public function getCountryState(): ?CountryStateEntity
    {
        return $this->countryState;
    }

    /**
     * @param CountryStateEntity|null $countryState
     */
    public function setCountryState(?CountryStateEntity $countryState): void
    {
        $this->countryState = $countryState;
    }

    /**
     * @return CustomerEntity|null
     */
    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    /**
     * @param CustomerEntity|null $customer
     */
    public function setCustomer(?CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return string|null
     */
    public function getShopUrl(): ?string
    {
        return $this->shopUrl;
    }

    /**
     * @param string|null $shopUrl
     */
    public function setShopUrl(?string $shopUrl): void
    {
        $this->shopUrl = $shopUrl;
    }

    /**
     * @return string|null
     */
    public function getMerchantUrl(): ?string
    {
        return $this->merchantUrl;
    }

    /**
     * @param string|null $merchantUrl
     */
    public function setMerchantUrl(?string $merchantUrl): void
    {
        $this->merchantUrl = $merchantUrl;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getOpeningHours(): ?string
    {
        return $this->openingHours;
    }

    /**
     * @param string|null $openingHours
     */
    public function setOpeningHours(?string $openingHours): void
    {
        $this->openingHours = $openingHours;
    }

    /**
     * @return float|null
     */
    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    /**
     * @param float|null $locationLat
     */
    public function setLocationLat(?float $locationLat): void
    {
        $this->locationLat = $locationLat;
    }

    /**
     * @return float|null
     */
    public function getLocationLon(): ?float
    {
        return $this->locationLon;
    }

    /**
     * @param float|null $locationLon
     */
    public function setLocationLon(?float $locationLon): void
    {
        $this->locationLon = $locationLon;
    }

    /**
     * @return string|null
     */
    public function getOriginId(): ?string
    {
        return $this->originId;
    }

    /**
     * @param string|null $originId
     */
    public function setOriginId(?string $originId): void
    {
        $this->originId = $originId;
    }

    /**
     * @return string|null
     */
    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    /**
     * @param string|null $streetNumber
     */
    public function setStreetNumber(?string $streetNumber): void
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string|null $countryCode
     */
    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }
}
