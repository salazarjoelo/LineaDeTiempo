<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Administrator\View\Item;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Object\CMSObject; // Para tipar $this->item

/**
 * Item View for Timeline component (Administrator) - Edit/Create form.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected ?Form $form = null;
    protected ?CMSObject $item = null; // O puedes tiparlo con tu clase Table: ?\Salazarjoelo\Component\Timeline\Administrator\Table\ItemTable
    // protected ?string $script = null; // Si necesitas añadir JS específico al formulario

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse.
     *
     * @return  void
     * @since   1.0.0
     */
    public function display(string $tpl = null): void
    {
        $this->form = $this->get('Form'); // Obtiene el objeto Form del ItemModel
        $this->item = $this->get('Item'); // Obtiene los datos del ítem del ItemModel
        // $this->script = $this->get('Script'); // Si el ItemModel genera algún script JS

        // Verificar errores (AdminModel los acumula)
        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            // throw new \RuntimeException(implode("\n", $errors), 500);
            return;
        }

        $this->addToolbar();

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
        $app = Factory::getApplication();
        $app->input->set('hidemainmenu', true); // Oculta el menú principal de Joomla en la vista de edición

        $user   = $app->getIdentity();
        $isNew  = ($this->item === null || !isset($this->item->id) || $this->item->id == 0);
        $itemId = $isNew ? 0 : (int) $this->item->id;
        
        // Determinar el asset para los permisos. Podría ser simplemente 'com_timeline'
        // o si tienes ACL por ítem 'com_timeline.item.ITEM_ID'
        $assetName = 'com_timeline'; // O $this->getModel()->typeAlias si está definido
        if (!$isNew) {
            $assetName .= '.item.' . $itemId;
        }
        $canDo = ContentHelper::getActions($assetName); // O ContentHelper::getActions('com_timeline') si los permisos son a nivel de componente

        ToolbarHelper::title(
            $isNew ? Text::_('COM_TIMELINE_MANAGER_ITEM_NEW_TITLE') : Text::_('COM_TIMELINE_MANAGER_ITEM_EDIT_TITLE'),
            $isNew ? 'plus-circle icon-plus-circle' : 'edit icon-edit' // Iconos
        );

        // Botones estándar para un formulario de edición
        // El permiso 'core.edit' en el asset del ítem, o 'core.create' en el asset del componente si es nuevo
        if ($isNew && $user->authorise('core.create', 'com_timeline')) {
            ToolbarHelper::apply('item.apply', 'JTOOLBAR_APPLY');
            ToolbarHelper::save('item.save', 'JTOOLBAR_SAVE');
            ToolbarHelper::save2new('item.save2new', 'JTOOLBAR_SAVE_AND_NEW');
        }
        if (!$isNew && $user->authorise('core.edit', $assetName)) {
             ToolbarHelper::apply('item.apply', 'JTOOLBAR_APPLY');
             ToolbarHelper::save('item.save', 'JTOOLBAR_SAVE');
             // Si tienes la lógica para 'Guardar como Copia'
             // if ($user->authorise('core.create', 'com_timeline')) {
             //    ToolbarHelper::save2copy('item.save2copy', 'JTOOLBAR_SAVE_AS_COPY');
             // }
        }

        // Botón Cancelar
        if (empty($itemId)) { // Si es nuevo
            ToolbarHelper::cancel('item.cancel', 'JTOOLBAR_CANCEL');
        } else {
            ToolbarHelper::cancel('item.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}