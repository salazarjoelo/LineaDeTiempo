<?php
/**
 * @package     Salazarjoelo\Component\LineaDeTiempo
 * @subpackage  com_lineadetiempo
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\LineaDeTiempo\Administrator\View\Item;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Object\CMSObject; // O tu clase Table si prefieres tipar $item más específicamente

/**
 * Item View for LineaDeTiempo component (Administrator) - Edit/Create form.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected ?Form $form = null;
    // Tipar con tu clase Table es más específico, pero CMSObject o stdClass también funcionan
    protected ?\Salazarjoelo\Component\LineaDeTiempo\Administrator\Table\ItemTable $item = null; 
    // protected ?string $script = null; // Para JS específico del formulario

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
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        // $this->script = $this->get('Script');

        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
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
        $app->input->set('hidemainmenu', true);

        $user   = $app->getIdentity();
        $isNew  = ($this->item === null || !isset($this->item->id) || $this->item->id == 0);
        $itemId = $isNew ? 0 : (int) $this->item->id;
        
        // Para los permisos, usa el $typeAlias definido en tu ItemModel si es consistente.
        // O construye el asset name directamente.
        $assetName = 'com_lineadetiempo.item' . ($isNew ? '' : '.' . $itemId);
        if ($isNew) { // Para un ítem nuevo, el permiso de creación se verifica sobre el componente.
            $canDoAsset = ContentHelper::getActions('com_lineadetiempo', 'component');
            $canDo = new \Joomla\CMS\Object\CMSObject(); // Crear un objeto para los permisos del ítem
            $canDo->set('core.create', $user->authorise('core.create', 'com_lineadetiempo'));
        } else {
            $canDo = ContentHelper::getActions('com_lineadetiempo', 'item', $itemId);
        }


        ToolbarHelper::title(
            $isNew ? Text::_('COM_LINEADETIEMPO_MANAGER_ITEM_NEW_TITLE') : Text::_('COM_LINEADETIEMPO_MANAGER_ITEM_EDIT_TITLE'),
            $isNew ? 'plus-circle icon-plus-circle' : 'edit icon-edit'
        );

        // El permiso 'core.edit' en el ítem existente, o 'core.create' en el componente si es nuevo.
        if (($isNew && $canDo->get('core.create')) || (!$isNew && $canDo->get('core.edit', null, $itemId))) {
            ToolbarHelper::apply('item.apply', 'JTOOLBAR_APPLY');
            ToolbarHelper::save('item.save', 'JTOOLBAR_SAVE');
        }
        
        // Guardar y Nuevo solo si es nuevo y tiene permiso de creación
        if ($isNew && $canDo->get('core.create')) {
            ToolbarHelper::save2new('item.save2new', 'JTOOLBAR_SAVE_AND_NEW');
        }
        
        // Guardar como Copia (si es existente y tiene permiso de creación)
        // if (!$isNew && $user->authorise('core.create', 'com_lineadetiempo')) {
        //    ToolbarHelper::save2copy('item.save2copy', 'JTOOLBAR_SAVE_AS_COPY');
        // }

        if (empty($itemId)) {
            ToolbarHelper::cancel('item.cancel', 'JTOOLBAR_CANCEL');
        } else {
            ToolbarHelper::cancel('item.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}