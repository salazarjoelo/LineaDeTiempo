<?php
/**
 * @package     Salazarjoelo\Component\LineaDeTiempo
 * @subpackage  com_lineadetiempo
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\LineaDeTiempo\Administrator\View\Items;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView; // Usar HtmlView como base es lo más común y flexible
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Pagination\Pagination;

/**
 * Items View for LineaDeTiempo component (Administrator) - List of items.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected ?array $items = null;
    protected ?Pagination $pagination = null;
    protected ?object $state = null;
    protected ?object $filterForm = null;
    protected ?array $activeFilters = null;
    // protected bool $sidebarExists = false; // Para controlar si se muestra el contenedor de la sidebar

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
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        // $this->sidebarExists = !empty($this->filterForm) || !empty($this->get('Sidebar')); // Comprueba si hay algo que mostrar en la sidebar

        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return;
        }

        $this->addToolbar();
        
        // Preparar y asignar la sidebar para que la plantilla la renderice
        // La plantilla puede decidir si usa $this->sidebar o HTMLHelper::_('sidebar.render')
        if (!empty($this->filterForm) || method_exists($this, 'getSidebar')) { // Revisar si hay filtros o un método getSidebar
            $this->sidebar = $this->getSidebar(); // El método getSidebar está en JViewLegacy, aquí lo emulamos o usamos directamente HTMLHelper en tmpl
            if (empty($this->sidebar) && $this->filterForm) { // Si getSidebar no devolvió nada pero hay filtros
                 ob_start();
                 HTMLHelper::_('sidebar.render', ['view' => $this]);
                 $this->sidebar = ob_get_clean();
            }
        }


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
        $canDo = ContentHelper::getActions('com_lineadetiempo'); // Asset principal del componente

        ToolbarHelper::title(Text::_('COM_LINEADETIEMPO_MANAGER_ITEMS_TITLE'), 'list-ul icon-list-ul');

        if ($canDo->get('core.create')) {
            ToolbarHelper::addNew('item.add', 'JTOOLBAR_NEW');
        }

        if ($canDo->get('core.edit')) {
            ToolbarHelper::editList('item.edit', 'JTOOLBAR_EDIT');
        }
        
        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('items.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('items.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            // Si tu tabla y modelo soportan archivado y papelera:
            // ToolbarHelper::archiveList('items.archive', 'JTOOLBAR_ARCHIVE');
            // ToolbarHelper::trash('items.trash', 'JTOOLBAR_TRASH');
        }

        if ($canDo->get('core.delete')) {
            ToolbarHelper::deleteList(Text::_('COM_LINEADETIEMPO_CONFIRM_DELETE_ITEMS_MSG'), 'items.delete', 'JTOOLBAR_DELETE');
        }

        if ($user->authorise('core.admin', 'com_lineadetiempo') || $user->authorise('core.options', 'com_lineadetiempo')) {
            ToolbarHelper::preferences('com_lineadetiempo');
        }
    }
    
    /**
     * Renders the sidebar. (Opcional, la plantilla puede usar HTMLHelper::_('sidebar.render') directamente)
     * Pero si necesitas más control, puedes generar el HTML de la sidebar aquí.
     *
     * @return  string  The HTML for the sidebar or empty string.
     * @since   1.0.0
     */
    /*
    protected function getSidebar(): string
    {
        if ($this->filterForm) {
            return LayoutHelper::render('joomla.searchtools.sidebar', ['view' => $this]);
        }
        return '';
    }
    */
}