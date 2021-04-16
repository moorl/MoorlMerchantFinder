<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Marker;

use Moorl\MerchantFinder\Core\Content\Aggregate\MarkerCategory\MarkerCategoryDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MarkerCustomer\MarkerCustomerDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MarkerProductManufacturer\MarkerProductManufacturerDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MarkerStock\MarkerStockDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MarkerTag\MarkerTagDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MarkerTranslation\MarkerTranslationDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\EditField;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\LabelProperty;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class MarkerDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'moorl_merchant_marker';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return MarkerCollection::class;
    }

    public function getEntityClass(): string
    {
        return MarkerEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new FkField('marker_id', 'markerId', MediaDefinition::class),
            new FkField('marker_shadow_id', 'markerShadowId', MediaDefinition::class),
            new FkField('marker_retina_id', 'markerRetinaId', MediaDefinition::class),

            new JsonField('marker_settings', 'markerSettings'),
            (new StringField('type', 'type'))->addFlags(new EditField('text')),
            (new StringField('name', 'name'))->addFlags(new EditField('text'), new Required()),

            (new ManyToOneAssociationField('marker', 'marker_id', MediaDefinition::class, 'id', true))->addFlags(new EditField(), new LabelProperty('fileName')),
            (new ManyToOneAssociationField('markerShadow', 'marker_shadow_id', MediaDefinition::class, 'id', true))->addFlags(new EditField(), new LabelProperty('fileName')),
            (new ManyToOneAssociationField('markerRetina', 'marker_retina_id', MediaDefinition::class, 'id', true))->addFlags(new EditField(), new LabelProperty('fileName')),

            new OneToManyAssociationField('merchants', MerchantDefinition::class, 'moorl_merchant_marker_id'),
        ]);
    }
}
