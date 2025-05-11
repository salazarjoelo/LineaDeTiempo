<?php
/**
 * @package     Salazarjoelo\Component\LineaDeTiempo
 * @subpackage  com_lineadetiempo
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\LineaDeTiempo\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
// use Joomla\CMS\Application\ApplicationHelper; // Para generar alias, si lo usaras

/**
 * Item Model for LineaDeTiempo component (Administrator)
 *
 * @since  1.0.0
 */
class ItemModel extends AdminModel
{
    /**
     * The type alias for this content type. This is used to build the asset name.
     */
    public string $typeAlias = 'com_lineadetiempo.item'; // Consistente con el nombre del componente

    /**
     * Method to get the table object, instantiating it if necessary.
     */
    public function getTable(string $name = 'Item', string $prefix = 'Administrator', array $options = []): Table
    {
        // Esto cargará Salazarjoelo\Component\LineaDeTiempo\Administrator\Table\ItemTable
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Method to get the form.
     */
    public function getForm(array $data = [], bool $loadData = true): Form|false
    {
        $form = $this->loadForm(
            $this->typeAlias, // Contexto: com_lineadetiempo.item
            'item',           // Nombre del archivo XML: item.xml
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            $this->setError(Text::_('JLIB_FORM_COULD_NOT_LOAD_FORM'));
            return false;
        }

        $user = Factory::getApplication()->getIdentity();
        $recordId = $this->getState($this->getName() . '.id');

        // Si el usuario no tiene permiso para cambiar el estado (y no es un ítem nuevo)
        if ($recordId && !$user->authorise('core.edit.state', $this->typeAlias . '.' . $recordId)) {
            $form->setFieldAttribute('state', 'disabled', 'true');
            $form->setFieldAttribute('state', 'filter', 'unset'); 
        }
        // Si es nuevo, el campo de ordenamiento no tiene sentido editarlo
        if (empty($recordId)){
            $form->setFieldAttribute('ordering', 'hidden', 'true'); // O 'disabled', 'true'
        }


        return $form;
    }

    /**
     * Method to get the data that should be injected into the form.
     */
    protected function loadFormData(): ?array
    {
        $data = Factory::getApplication()->getUserState($this->typeAlias . '.edit.data', null);

        if ($data === null) {
            $data = $this->getItem();
        }
        
        if (is_object($data)) {
            $this->preprocessData($this->typeAlias, $data); // Permite a plugins modificar los datos
            $data = ArrayHelper::fromObject($data);
        }
        
        return $data;
    }

    /**
     * Method to save the form data.
     */
    public function save(array $data): bool
    {
        $app    = Factory::getApplication();
        $user   = $app->getIdentity();
        $table  = $this->getTable();
        $key    = $table->getKeyName();
        $pk     = $app->input->getInt($key); // Obtener el ID desde el input por si no viene en $data
        $isNew  = empty($pk); // Determinar si es nuevo basándose en el ID del input o $data

        if ($isNew && isset($data[$key])) { // Si es nuevo, pero $data tiene un ID (ej. 0), forzar $pk a 0
            $pk = (int) $data[$key];
            if ($pk === 0) {
                $isNew = true;
            }
        }
        if (!$isNew) {
            $data[$key] = $pk; // Asegurar que el ID esté en $data para la validación
        }


        $form   = $this->getForm($data, false);
        if (!$form) {
            return false;
        }

        $validData = $this->validateData($form, $data);
        if ($validData === false) {
            return false;
        }
        
        // Comprobar permisos
        $assetName = $this->typeAlias . ($isNew ? '' : '.' . $validData[$key]);
        if ($isNew) {
            if (!$user->authorise('core.create', 'com_lineadetiempo')) { // Permiso a nivel componente para crear
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'));
                return false;
            }
        } else {
            if (!$user->authorise('core.edit', $assetName) && !$user->authorise('core.edit.own', $assetName)) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
                return false;
            }
        }
        
        // Lógica de prepareTable de J3 (ej. set created_by) ya está en ItemTable::check()
        // Enlazar y guardar
        try {
            if (!$table->save($validData)) { // El método save de JTable (AdminModel lo usa) llama a bind, check, store
                $this->setError($table->getError());
                return false;
            }
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        // Guardar reglas ACL si existen en los datos validados y el asset_id está disponible
        if (isset($validData['rules']) && is_array($validData['rules']) && $table->$key > 0 && $table->asset_id > 0) {
            $asset = Table::getInstance('Asset', 'JTable');
            if ($asset->load((int) $table->asset_id)) {
                if (!$asset->save($validData['rules'])) { // El método save de JTableAsset formatea las reglas
                    $this->setError($asset->getError());
                    // No necesariamente retornar false aquí, el ítem principal se guardó. Quizás loguear.
                }
            }
        }
        
        $pkName = $table->getKeyName();
        if (isset($table->$pkName)) {
            $this->setState($this->getName() . '.id', $table->$pkName);
        }

        $this->cleanCache();
        $app->setUserState($this->typeAlias . '.edit.data', null);

        return true;
    }

    /**
     * Method to delete one or more records.
     */
    public function delete(array &$pks): bool
    {
        $user  = Factory::getApplication()->getIdentity();
        $table = $this->getTable();

        foreach ($pks as $i => $pk) {
            $pk = (int) $pk;
            if (!$table->load($pk)) {
                $this->setError($table->getError());
                return false;
            }

            $assetName = $this->typeAlias . '.' . $pk;
            if (!$user->authorise('core.delete', $assetName)) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
                return false;
            }

            if (!$table->delete($pk)) {
                $this->setError($table->getError());
                return false;
            }
        }
        
        $this->cleanCache();
        return true;
    }
}