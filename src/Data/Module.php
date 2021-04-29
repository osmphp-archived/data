<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Array_;
use Osm\Core\BaseModule;
use Osm\Framework\Cache\Descendants;
use function Osm\__;

/**
 * @property string[] $models
 * @property Descendants $descendants
 */
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Framework\Cache\Module::class,
        \Osm\Framework\Db\Module::class,
        \Osm\Framework\Http\Module::class,
        \Osm\Framework\Migrations\Module::class,
        \Osm\Framework\Search\Module::class,
    ];

    public static array $traits = [
        App::class => Traits\AppTrait::class,
    ];

    protected function get_models(): array|Array_ {
        return new Array_($this->descendants->byName(Model::class),
            fn($key) => __("Undefined model class ':class'",
                ['class' => $key]));
    }

    protected function get_descendants(): Descendants {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->descendants;
    }
}