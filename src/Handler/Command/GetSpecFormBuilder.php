<?php namespace Frp\SpecFormExtension\Handler\Command;

use Anomaly\ConfigurationModule\Configuration\Contract\ConfigurationRepositoryInterface;
use Anomaly\FilesModule\File\Command\GetFile;
use Anomaly\FilesModule\File\Contract\FileInterface;
use Anomaly\FilesModule\File\FileDownloader;
use Anomaly\FormsModule\Form\Contract\FormInterface;
use Anomaly\Streams\Platform\Support\Template;
use Frp\SpecFormExtension\Handler\SpecFormBuilder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Redirector;

class GetSpecFormBuilder
{

    use DispatchesJobs;

    /**
     * The form instance.
     *
     * @var FormInterface
     */
    protected $form;

    /**
     * Create a new GetSpecFormBuilder instance.
     *
     * @param FormInterface $form
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * Handle the command.
     *
     * @param SpecFormBuilder $builder
     * @return SpecFormBuilder
     */
    public function handle(SpecFormBuilder $builder, Redirector $redirect)
    {
        $stream = $this->form->getFormEntriesStream();

        $builder->on(
            'saved',
            function (
                ConfigurationRepositoryInterface $configuration,
                FileDownloader $downloader,
                Template $template
            ) use ($builder) {

                /* @var FileInterface $file */
                $file = $this->dispatch(
                    new GetFile(
                        array_get(
                            $configuration->findByKeyAndScopeOrNew(
                                $this->form->getFormHandler()->getNamespace('spec_template'),
                                $this->form->getId()
                            )->getAttributes(),
                            'value'
                        )
                    )
                );

                $response = $downloader->download($file);

                $builder->setFormResponse(
                    $response->setContent(
                        $template->render(
                            str_replace(
                                ['\{\{', '\}\}'],
                                ['{{', '}}'],
                                $response->getContent()
                            ),
                            [
                                'input' => $builder->getFormEntry(),
                            ]
                        )->render()
                    )
                );
            }
        );

//        $builder->on(
//            'saved',
//            function (FormMailer $mailer, FormAutoresponder $autoresponder) use ($builder) {
//                $mailer->send($this->form, $builder);
//                $autoresponder->send($this->form, $builder);
//            }
//        );

        return $builder
            ->setActions(['submit'])
            ->setModel($stream->getEntryModel())
            ->setOption('panel_class', 'section')
            ->setOption('enable_defaults', false)
            ->setOption('success_message', $this->form->getSuccessMessage() ?: false)
            ->setOption('redirect', $this->form->getSuccessRedirect() ?: $redirect->back());
    }
}
