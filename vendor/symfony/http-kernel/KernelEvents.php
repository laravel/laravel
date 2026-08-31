<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel;

use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;

/**
 * Contains all events thrown in the HttpKernel component.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class KernelEvents
{
    /**
     * The REQUEST event occurs at the very beginning of request
     * dispatching.
     *
     * This event allows you to create a response for a request before any
     * other code in the framework is executed.
     *
     * @Event("Symfony\Component\HttpKernel\Event\RequestEvent")
     */
    public const REQUEST = 'kernel.request';

    /**
     * The EXCEPTION event occurs when an uncaught exception appears.
     *
     * This event allows you to create a response for a thrown exception or
     * to modify the thrown exception.
     *
     * @Event("Symfony\Component\HttpKernel\Event\ExceptionEvent")
     */
    public const EXCEPTION = 'kernel.exception';

    /**
     * The CONTROLLER event occurs once a controller was found for
     * handling a request.
     *
     * This event allows you to change the controller that will handle the
     * request.
     *
     * @Event("Symfony\Component\HttpKernel\Event\ControllerEvent")
     */
    public const CONTROLLER = 'kernel.controller';

    /**
     * The CONTROLLER_ARGUMENTS event occurs once controller arguments have been resolved.
     *
     * This event allows you to change the arguments that will be passed to
     * the controller.
     *
     * @Event("Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent")
     */
    public const CONTROLLER_ARGUMENTS = 'kernel.controller_arguments';

    /**
     * The VIEW event occurs when the return value of a controller
     * is not a Response instance.
     *
     * This event allows you to create a response for the return value of the
     * controller.
     *
     * @Event("Symfony\Component\HttpKernel\Event\ViewEvent")
     */
    public const VIEW = 'kernel.view';

    /**
     * The RESPONSE event occurs once a response was created for
     * replying to a request.
     *
     * This event allows you to modify or replace the response that will be
     * replied.
     *
     * @Event("Symfony\Component\HttpKernel\Event\ResponseEvent")
     */
    public const RESPONSE = 'kernel.response';

    /**
     * The FINISH_REQUEST event occurs when a response was generated for a request.
     *
     * This event allows you to reset the global and environmental state of
     * the application, when it was changed during the request.
     *
     * @Event("Symfony\Component\HttpKernel\Event\FinishRequestEvent")
     */
    public const FINISH_REQUEST = 'kernel.finish_request';

    /**
     * The TERMINATE event occurs once a response was sent.
     *
     * This event allows you to run expensive post-response jobs.
     *
     * @Event("Symfony\Component\HttpKernel\Event\TerminateEvent")
     */
    public const TERMINATE = 'kernel.terminate';

    /**
     * Event aliases.
     *
     * These aliases can be consumed by RegisterListenersPass.
     */
    public const ALIASES = [
        ControllerArgumentsEvent::class => self::CONTROLLER_ARGUMENTS,
        ControllerEvent::class => self::CONTROLLER,
        ResponseEvent::class => self::RESPONSE,
        FinishRequestEvent::class => self::FINISH_REQUEST,
        RequestEvent::class => self::REQUEST,
        ViewEvent::class => self::VIEW,
        ExceptionEvent::class => self::EXCEPTION,
        TerminateEvent::class => self::TERMINATE,
    ];
}
