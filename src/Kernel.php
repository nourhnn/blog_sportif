<?php

namespace App;


use App\DependencyInjection\AppExtension;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;


class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function __construct(string $environment, bool $debug)
    {
        // Charge le fichier .env s'il existe
        if (file_exists(dirname(__DIR__).'/.env')) {
            (new Dotenv())->loadEnv(dirname(__DIR__).'/.env');
        }

        parent::__construct($environment, $debug);
    }

    public function registerBundles(): array
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Twig\Extra\TwigExtraBundle\TwigExtraBundle(),
        ];

        if ('dev' === $this->getEnvironment()) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Symfony\Bundle\MakerBundle\MakerBundle();
        }
        return $bundles;
    }


    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->registerExtension(new AppExtension());
    }


    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(__DIR__.'/../config/framework.yaml');
        $container->import(__DIR__.'/../config/services.yaml');
        $container->import(__DIR__.'/../config/doctrine.yaml');
        $container->import(__DIR__.'/../config/doctrine_migrations.yaml');
        $container->import(__DIR__.'/../config/twig.yaml');
        $container->import(__DIR__.'/../config/validator.yaml');
        $container->import(__DIR__.'/../config/packages/security.yaml');

        // register all classes in /src/ as service
        $container->services()
            ->load('App\\', __DIR__.'/*')
            ->autowire()
            ->autoconfigure()
        ;

        // configure WebProfilerBundle only if the bundle is enabled
        if (isset($this->bundles['WebProfilerBundle'])) {
            $container->extension('web_profiler', [
                'toolbar' => true,
                'intercept_redirects' => false,
            ]);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // import the WebProfilerRoutes, only if the bundle is enabled
        if (isset($this->bundles['WebProfilerBundle'])) {

            $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.xml')->prefix('/_wdt');

            $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.xml')->prefix('/_profiler');

        }
        // load the routes defined as PHP attributes

        // (use 'annotation' as the second argument if you define routes as annotations)

        $routes->import(__DIR__.'/Controller/', 'attribute');
        $routes->import(__DIR__ . '/../config/routes.yaml');

    }


    // optional, to use the standard Symfony cache directory

    public function getCacheDir(): string
    {
        return __DIR__.'/../var/cache/'.$this->getEnvironment();
    }

    // optional, to use the standard Symfony logs directory

    public function getLogDir(): string
    {
        return __DIR__.'/../var/log';
    }
}