<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Site\Dispatcher;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

/**
 * Site-side main entry point for Timeline component.
 *
 * @since  1.0.0
 */
class TimelineComponent extends MVCComponent
{
    /**
     * Constructor.
     *
     * @param   CMSApplication       $application  The CMS Application.
     * @param   MVCFactoryInterface  $factory      The MVC factory.
     * @param   ?string              $basePath     The base path for the component.
     *
     * @since   1.0.0
     */
    public function __construct(CMSApplication $application, MVCFactoryInterface $factory, ?string $basePath = null)
    {
        parent::__construct($application, $factory, $basePath);
    }
}