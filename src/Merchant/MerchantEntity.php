<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Merchant;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\Country\Aggregate\CountryState\CountryStateEntity;
use Shopware\Core\System\Country\CountryEntity;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\Salutation\SalutationEntity;

class MerchantEntity extends Entity
{

    use EntityIdTrait;

    /**
     * @var string
     */
    protected $mediaId;

    /**
     * @var string
     */
    protected $markerId;

    /**
     * @var string
     */
    protected $markerShadowId;

    /**
     * @var string
     */
    protected $markerSettings;

    /**
     * @return string
     */
    public function getMarkerShadowId(): string
    {
        return $this->markerShadowId;
    }

    /**
     * @param string $markerShadowId
     */
    public function setMarkerShadowId(string $markerShadowId): void
    {
        $this->markerShadowId = $markerShadowId;
    }

    /**
     * @return string
     */
    public function getMarkerSettings(): string
    {
        return $this->markerSettings;
    }

    /**
     * @param string $markerSettings
     */
    public function setMarkerSettings(string $markerSettings): void
    {
        $this->markerSettings = $markerSettings;
    }

    /**
     * @var string
     */
    protected $salesChannelId;

    /**
     * @var string
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
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string|null
     */
    protected $company;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var bool
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
     * @var string
     */
    protected $countryId;

    /**
     * @var string|null
     */
    protected $countryStateId;

    /**
     * @var string
     */
    protected $salutationId;

    /**
     * @var string
     */
    protected $zipcode;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string|null
     */
    protected $department;

    /**
     * @var string
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
     * @var string
     */
    protected $shopUrl;

    /**
     * @var string
     */
    protected $merchantUrl;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $openingHours;

    /**
     * @var float
     */
    protected $locationLat;

    /**
     * @var float
     */
    protected $locationLon;

    /**
     * @var string
     */
    protected $originId;

    /**
     * @var string
     */
    protected $streetNumber;

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getOpeningHours(): string
    {
        return $this->openingHours;
    }

    /**
     * @param string $openingHours
     */
    public function setOpeningHours(string $openingHours): void
    {
        $this->openingHours = $openingHours;
    }

    /**
     * @return string
     */
    public function getMarkerId(): string
    {
        return $this->markerId;
    }

    /**
     * @param string $mediaId
     */
    public function setMarkerId(string $markerId): void
    {
        $this->markerId = $markerId;
    }

    /**
     * @return string
     */
    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    /**
     * @param string $mediaId
     */
    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
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
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
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
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
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
     * @return string
     */
    public function getCountryId(): string
    {
        return $this->countryId;
    }

    /**
     * @param string $countryId
     */
    public function setCountryId(string $countryId): void
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
     * @return string
     */
    public function getSalutationId(): string
    {
        return $this->salutationId;
    }

    /**
     * @param string $salutationId
     */
    public function setSalutationId(string $salutationId): void
    {
        $this->salutationId = $salutationId;
    }

    /**
     * @return string
     */
    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    /**
     * @param string $zipcode
     */
    public function setZipcode(string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
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
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
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
     * @return string
     */
    public function getShopUrl(): string
    {
        return $this->shopUrl;
    }

    /**
     * @param string $shopUrl
     */
    public function setShopUrl(string $shopUrl): void
    {
        $this->shopUrl = $shopUrl;
    }

    /**
     * @return string
     */
    public function getMerchantUrl(): string
    {
        return $this->merchantUrl;
    }

    /**
     * @param string $merchantUrl
     */
    public function setMerchantUrl(string $merchantUrl): void
    {
        $this->merchantUrl = $merchantUrl;
    }

    /**
     * @return float
     */
    public function getLocationLat(): float
    {
        return $this->locationLat;
    }

    /**
     * @param float $locationLat
     */
    public function setLocationLat(float $locationLat): void
    {
        $this->locationLat = $locationLat;
    }

    /**
     * @return float
     */
    public function getLocationLon(): float
    {
        return $this->locationLon;
    }

    /**
     * @param float $locationLon
     */
    public function setLocationLon(float $locationLon): void
    {
        $this->locationLon = $locationLon;
    }

    /**
     * @return string
     */
    public function getOriginId(): string
    {
        return $this->originId;
    }

    /**
     * @param string $originId
     */
    public function setOriginId(string $originId): void
    {
        $this->originId = $originId;
    }

    /**
     * @return string
     */
    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber(string $streetNumber): void
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    /**
     * @param string $salesChannelId
     */
    public function setSalesChannelId(string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    /**
     * @return string
     */
    public function getCustomerGroupId(): string
    {
        return $this->customerGroupId;
    }

    /**
     * @param string $customerGroupId
     */
    public function setCustomerGroupId(string $customerGroupId): void
    {
        $this->customerGroupId = $customerGroupId;
    }

}