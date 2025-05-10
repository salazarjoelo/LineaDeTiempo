<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Administrator\View\Items;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper; // Para permisos
use Joomla\CMS\Helper\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Layout\LayoutHelper; // Para renderizar filtros y paginación
use Joomla\CMS\Pagination\Pagination; // Para tipado de $this->pagination

/**
 * Items View for Timeline component (Administrator) - List of items.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected ?array $items = null;
    protected ?Pagination $pagination = null;
    protected ?object $state = null; // Estado del modelo (filtros, ordenación, etc.)
    protected ?object $filterForm = null; // Objeto JForm para los filtros
    protected ?array $activeFilters = null; // Array de filtros activos

    /**
     * Display the view.
     *
     * @param   string  $tpl  The name of the template file to parse.
     *
     * @return  void
     * @since   1.0.0
     */
    public function display(string $tpl = null): void
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');    // Desde ListModel
        $this->activeFilters = $this->get('ActiveFilters'); // Desde ListModel

        // Verificar errores (ListModel los acumula)
        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            // Podrías lanzar una excepción aquí si prefieres un manejo de error más drástico
            // throw new \RuntimeException(implode("\n", $errors), 500);
            return;
        }

        $this->addToolbar();
        $this->addSidebar(); // Para mostrar los filtros en la barra lateral

        // Asignar el autor del componente para la plantilla (ej. pie de página)
        // $this->componentAuthor = ComponentHelper::getParams('com_timeline')->get('author', 'Joel Salazar');

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     * @since   1.0.0
     */
    protected function addToolbar(): void
    {
        $app   = Factory::getApplication();
        $user  = $app->getIdentity();
        $canDo = ContentHelper::getActions('com_timeline'); // Permisos a nivel de componente

        ToolbarHelper::title(Text::_('COM_TIMELINE_MANAGER_ITEMS_TITLE'), 'list-ul icon-list-ul'); // Título y icono

        if ($canDo->get('core.create')) {
            ToolbarHelper::addNew('item.add', 'JTOOLBAR_NEW'); // Tarea del ItemController
        }

        if ($canDo->get('core.edit')) {
            ToolbarHelper::editList('item.edit', 'JTOOLBAR_EDIT'); // Tarea del ItemController
        }
        
        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('items.publish', 'JTOOLBAR_PUBLISH', true);     // Tarea del ItemsController
            ToolbarHelper::unpublish('items.unpublish', 'JTOOLBAR_UNPUBLISH', true); // Tarea del ItemsController
            // Considera añadir 'archive' y 'trash' si los usas en tu campo 'state'
            // ToolbarHelper::archiveList('items.archive', 'JTOOLBAR_ARCHIVE');
            // ToolbarHelper::trash('items.trash', 'JTOOLBAR_TRASH');
        }

        if ($canDo->get('core.delete')) {
            ToolbarHelper::deleteList(Text::_('COM_TIMELINE_CONFIRM_DELETE_ITEMS_MSG'), 'items.delete', 'JTOOLBAR_DELETE'); // Tarea del ItemsController
        }

        // Botón de Opciones del componente
        if ($user->authorise('core.admin', 'com_timeline') || $user->authorise('core.options', 'com_timeline')) {
            ToolbarHelper::preferences('com_timeline');
        }
    }
    
    /**
     * Adds the sidebar with search tools.
     *
     * @return  void
     * @since   1.0.0
     */
    protected function addSidebar(): void
    {
        // Esto renderiza la barra lateral estándar de Joomla con los filtros.
        // Requiere que $this->filterForm y $this->activeFilters estén disponibles (cargados desde el ItemsModel).
        if ($this->filterForm !== null) {
             HTMLHelper::_('sidebar.render', ['view' => $this]);
        }
    }
}