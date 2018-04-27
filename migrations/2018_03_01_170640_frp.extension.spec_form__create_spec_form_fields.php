<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class FrpExtensionSpecFormCreateSpecFormFields extends Migration
{

    /**
     * The addon fields.
     *
     * @var array
     */
    protected $fields = [
        'name' => 'frp.field_type.text',
        'slug' => [
            'type' => 'frp.field_type.slug',
            'config' => [
                'slugify' => 'name',
                'type' => '_'
            ],
        ],
    ];

}
