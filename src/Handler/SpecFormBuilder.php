<?php namespace Frp\SpecFormExtension\Handler;

use Anomaly\Streams\Platform\Ui\Form\FormBuilder;

/**
 * Class SpecFormBuilder
 *
 * @link          http://frp.is/streams-platform
 * @author        FrpLabs, Inc. <hello@frp.is>
 * @author        Ryan Thompson <ryan@frp.is>
 * @package       Frp\SpecFormExtension\Handler
 */
class SpecFormBuilder extends FormBuilder
{

    /**
     * The form options.
     *
     * @var array
     */
    protected $options = [
        'breadcrumb' => false,
    ];

}
