<?php namespace Frp\SpecFormExtension;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\FormsModule\Form\Contract\FormInterface;
use Anomaly\FormsModule\Form\Handler\Contract\FormHandlerExtensionInterface;
use Anomaly\Streams\Platform\Addon\Extension\Extension;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Frp\SpecFormExtension\Handler\Command\GetSpecFormBuilder;

class SpecFormExtension extends Extension implements FormHandlerExtensionInterface
{

    /**
     * @var null|string
     */
    protected $provides = 'anomaly.module.forms::form_handler.spec';

    /**
     * Return the form's builder instance.
     *
     * @param FormInterface $form
     * @return FormBuilder
     */
    public function builder(FormInterface $form)
    {
        return $this->dispatch(new GetSpecFormBuilder($form));
    }

    /**
     * Integrate the form handler's services
     * with the primary form's builder instance.
     *
     * @param FormBuilder $builder
     */
    public function integrate(FormBuilder $builder)
    {
        /* @var ConfigurationRepositoryInterface $configuration */
        $configuration = app(ConfigurationRepositoryInterface::class);

        $value = array_get(
            $configuration->findByKeyAndScopeOrNew(
                $this->getNamespace('spec_template'),
                request('id')
            )->getAttributes(),
            'value'
        );

        $builder->addField(
            'spec_template',
            [
                'required' => true,
                'save'     => false,
                'value'    => $value,
                'label'    => 'Spec Template',
                'type'     => 'anomaly.field_type.file',
                'config'   => [
                    'folders' => [
                        'templates',
                    ],
                ],
            ]
        );

        $builder->addSection(
            'spec',
            [
                'fields' => [
                    'spec_template',
                ],
            ]
        );

        $builder->on(
            'saved',
            function () use ($configuration, $builder) {
                $configuration->set(
                    $this->getNamespace('spec_template'),
                    $builder->getFormEntryId(),
                    $builder->getPostValue('spec_template')
                );
            }
        );

        return $builder;
    }
}
