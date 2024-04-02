<?php

declare(strict_types=1);

namespace Odandb\XhprofBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Xhgui\Profiler\Config;
use Xhgui\Profiler\Profiler;

class KernelEventSubscriber implements EventSubscriberInterface
{
    private ?Profiler $profiler = null;
    private ?Config $config;

    public function __construct(?Config $config = null)
    {
        $this->config = $config;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (mb_strpos(get_class($controller), 'WebProfilerBundle') !== false) {
            return;
        }

        if (null !== $this->config) {
            $this->profiler = new Profiler($this->config);
            $this->profiler->start(false);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->profiler instanceof Profiler) {
            $data = $this->profiler->stop();
            $event->getRequest()->attributes->set('_xhprof_data', $data);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
