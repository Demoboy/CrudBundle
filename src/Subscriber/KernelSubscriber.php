<?php
/**
 * This file is part of the KMJ Crud package.
 * Copyright (c) Kaelin Jacobson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2019
 */

declare(strict_types = 1);

namespace KMJ\CrudBundle\Subscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;


/**
 * Subscribes to kernel events
 *
 * @package KMJ\CrudBundle\Subscriber
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class KernelSubscriber implements EventSubscriberInterface
{

    /**
     * @var bool
     */
    private $enabled;

    /**
     * KernelSubscriber constructor.
     *
     * @param bool $enabled
     */
    public function __construct(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }

    /**
     * Determines the content type of the request, if the header application/json is set, the Crud controllers will
     * prefer to provide their response in json instead of html.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$this->enabled || !$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (($contentType = $request->headers->get('Content-Type')) && $contentType === 'application/json') {
            $request->setRequestFormat('json');
        }
    }
}