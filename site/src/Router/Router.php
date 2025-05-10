<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Site\Router;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterInterface;
use Joomla\CMS\Component\Router\Rules\RulesInterface;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Menu\Menu;
use Joomla\CMS\Menu\MenuItem;
use Joomla\Database\DatabaseInterface;

/**
 * Router class for com_timeline (Site)
 *
 * @since  1.0.0
 */
class Router implements RouterInterface
{
    protected SiteApplication $app;
    protected Menu $menu;
    /** @var array<RulesInterface> */
    protected array $rules;

    /**
     * Constructor.
     *
     * @param   SiteApplication    $app   The application object.
     * @param   Menu               $menu  The menu object.
     * @param   DatabaseInterface  $db    The database object.
     *
     * @since   1.0.0
     */
    public function __construct(SiteApplication $app, Menu $menu, DatabaseInterface $db)
    {
        $this->app  = $app;
        $this->menu = $menu;

        $this->rules = [
            new StandardRules($this),
            new NomenuRules($this),
        ];
    }

    public function build(array &$query): array
    {
        $segments = [];
        foreach ($this->rules as $rule) {
            $segments = $rule->build($query);
            if (!empty($segments)) {
                break;
            }
        }
        return $segments;
    }

    public function parse(array &$segments): array
    {
        $vars = [];
        foreach ($this->rules as $rule) {
            $vars = $rule->parse($segments);
            if (!empty($vars)) {
                break;
            }
        }
        return $vars;
    }

    public function preprocess(array &$query): void
    {
        foreach ($this->rules as $rule) {
            $rule->preprocess($query);
        }
    }

    public function getActiveMenuItem(array $query = []): ?MenuItem
    {
        if (empty($query['option']) || $query['option'] !== 'com_timeline') {
            return null;
        }
        return $this->menu->getActive() ?: $this->menu->getItem((int) ($query['Itemid'] ?? 0));
    }

    public function getComponentMenuItems(): array
    {
        return $this->menu->getItems(['link'], ['index.php?option=com_timeline']);
    }
}