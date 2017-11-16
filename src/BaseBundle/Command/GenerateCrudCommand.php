<?php

namespace BaseBundle\Command;

use BaseBundle\Base\BaseCommand;
use BaseBundle\Twig\Extension\CaseExtension;
use CaseHelper\CaseHelperFactory;
use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCrudCommand extends BaseCommand
{
    private $input;
    private $output;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('generate:crud')
            ->setDescription('Generate a single-page crud from an entity')
            ->addArgument('entity', InputArgument::REQUIRED, 'Entity path (ex: AppBundle:User)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $o = CaseHelperFactory::make(CaseHelperFactory::INPUT_TYPE_PASCAL_CASE);

        $entity  = $input->getArgument('entity');
        $factory = new DisconnectedMetadataFactory($this->get('doctrine'));
        $meta    = $factory->getClassMetadata($entity)->getMetadata()[0];

        $columns      = $meta->fieldMappings;
        $bundle       = substr($meta->getName(), 0, strpos($meta->getName(), '\\'));
        $bundleName   = substr($bundle, 0, -6);
        $bundleDir    = realpath(__DIR__.'/../../'.$bundle);
        $entityName   = substr($meta->rootEntityName, strrpos($meta->rootEntityName, '\\') + 1);
        $entityPrefix = strtolower(substr($entityName, 0, 1));
        $routePrefix  = strtolower($o->toSnakeCase($bundleName).'_'.$o->toSnakeCase($entityName)).'s';

        $context = [
            'columns' => $columns,

            // AppBundle
            'bundle' => $bundle,

            // App
            'bundleName' => $bundleName,

            // AppBundle:Office
            'entity' => $entity,

            // Office
            'entityName' => $entityName,

            // o
            'entityPrefix' => $entityPrefix,

            // app_office
            'routePrefix' => $routePrefix,
        ];

        $this->_renderFile(
            'controller.twig',
            "{$bundleDir}/Controller/{$entityName}sController.php",
            $context
        );

        $this->_renderFile(
            'view.twig',
            "{$bundleDir}/Resources/views/{$entityName}s/list.html.twig",
            $context
        );

        foreach (glob($bundleDir.'/Resources/translations/messages.*.yml') as $file) {
            $context['file'] = file_get_contents($file);

            $this->_renderFile(
                 'translations.twig',
                 "{$bundleDir}/Resources/translations/".basename($file),
                 $context
            );
        }
    }

    /*
     * ******************************************************
     * Code below taken from:
     * Sensio\Bundle\GeneratorBundle\Generator\Generator
     * ******************************************************
     */

    protected function _getTwigEnvironment()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem([__DIR__.'/../Resources/skeleton']), [
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => true,
            'autoescape'       => false,
        ]);

        $twig->addExtension(new CaseExtension());

        return $twig;
    }

    protected function _render($template, $parameters)
    {
        $twig = $this->_getTwigEnvironment();

        return $twig->render($template, $parameters);
    }

    protected function _renderFile($template, $target, $parameters)
    {
        $this->_mkdir(dirname($target));

        return $this->_dump($target, $this->_render($template, $parameters));
    }

    protected function _mkdir($dir, $mode = 0777, $recursive = true)
    {
        if (!is_dir($dir)) {
            mkdir($dir, $mode, $recursive);
            $this->output->writeln(sprintf('  <fg=green>created</> %s', $this->_relativizePath($dir)));
        }
    }

    protected function _dump($filename, $content)
    {
        if (file_exists($filename)) {
            $this->output->writeln(sprintf('  <fg=yellow>updated</> %s', $this->_relativizePath($filename)));
        } else {
            $this->output->writeln(sprintf('  <fg=green>created</> %s', $this->_relativizePath($filename)));
        }

        return file_put_contents($filename, $content);
    }

    protected function _relativizePath($absolutePath)
    {
        $relativePath = str_replace(getcwd(), '.', $absolutePath);

        return is_dir($absolutePath) ? rtrim($relativePath, '/').'/' : $relativePath;
    }
}
