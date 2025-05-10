<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Application\ApplicationHelper; // Para generar el alias si es necesario

/**
 * Item Model for Timeline component (Administrator)
 *
 * @since  1.0.0
 */
class ItemModel extends AdminModel
{
    /**
     * The type alias for this content type. This is used to build the asset name.
     *
     * @var    string
     * @since  1.0.0
     */
    public string $typeAlias = 'com_timeline.item';

    /**
     * Method to get the table object, instantiating it if necessary.
     *
     * @param   string  $name    The name of the table class.
     * @param   string  $prefix  The class prefix.
     * @param   array   $options Configuration array for the model.
     *
     * @return  Table  A Table object.
     *
     * @since   1.0.0
     */
    public function getTable(string $name = 'Item', string $prefix = 'Administrator', array $options = []): Table
    {
        // Asegúrate que el nombre de la clase de la tabla sea:
        // Salazarjoelo\Component\Timeline\Administrator\Table\ItemTable
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Method to get the form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  Form|false  A Form object on success, false on failure.
     *
     * @since   1.0.0
     */
    public function getForm(array $data = [], bool $loadData = true): Form|false
    {
        // Get the form.
        $form = $this->loadForm(
            $this->typeAlias, // Contexto del formulario (ej: com_timeline.item)
            'item',           // Nombre del archivo XML del formulario (item.xml)
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            $this->setError(Text::_('JLIB_FORM_COULD_NOT_LOAD_FORM'));
            return false;
        }

        // Modificar el formulario en tiempo de ejecución si es necesario
        // Por ejemplo, si es un ítem existente y el usuario no puede cambiar el estado
        $user = Factory::getApplication()->getIdentity();
        $recordId = $this->getState($this->getName() . '.id');

        if (!$user->authorise('core.edit.state', $this->typeAlias . ($recordId ? '.' . $recordId : ''))) {
            $form->setFieldAttribute('state', 'disabled', 'true');
            $form->setFieldAttribute('state', 'filter', 'unset'); // No permitir que se guarde si no tiene permiso
        }
        
        // Si es un nuevo ítem, podrías querer ocultar ciertos campos o establecer valores
        if (empty($recordId)) {
            // $form->setFieldAttribute('ordering', 'disabled', 'true'); // El ordening se calcula al guardar
        }


        return $form;
    }

    /**
     * Method to get the data that should be injected into the form.
     *
     * @return  array|null  The data for the form.
     *
     * @since   1.0.0
     */
    protected function loadFormData(): ?array
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState($this->typeAlias . '.edit.data', null);

        // Si no hay datos en sesión, cargar desde la base de datos
        if ($data === null) {
            $data = $this->getItem();
        }
        
        // Convertir el objeto a array si es necesario para el formulario
        if (is_object($data)) {
            // Antes de convertir, asegurar que las propiedades coincidan con los campos del formulario
            // Esto es importante si la tabla tiene más campos que el formulario o nombres diferentes
            $this->preprocessData($this->typeAlias, $data);
            $data = ArrayHelper::fromObject($data);
        }
        
        return $data;
    }

    /**
     * Method to preprocess the form.
     * Estándar en AdminModel, pero puedes añadir lógica aquí si es necesario.
     *
     * @param   Form   $form   A Form object.
     * @param   mixed  $data   The data expected for the form.
     * @param   string $group  The name of the plugin group to import.
     *
     * @return  void
     *
     * @see     \Joomla\CMS\MVC\Model\FormModel::preprocessForm
     * @since   1.0.0
     *
     * @throws  \Exception if there is an error in the form event.
     */
    protected function preprocessForm(Form $form, $data, string $group = 'content'): void
    {
        // Lógica de pre-procesamiento del formulario si es necesaria
        // Por ejemplo, si tienes campos dependientes o necesitas cargar opciones dinámicamente.
        
        parent::preprocessForm($form, $data, $group);
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  bool  True on success, false on failure.
     *
     * @since   1.0.0
     */
    public function save(array $data): bool
    {
        $app    = Factory::getApplication();
        $user   = $app->getIdentity();
        $table  = $this->getTable();
        $isNew  = empty($data['id']);
        $form   = $this->getForm($data, false); // Cargar el formulario sin datos, para validación

        if (!$form) {
            return false;
        }

        // Validar los datos del formulario
        $validData = $this->validateData($form, $data);
        if ($validData === false) {
            return false;
        }
        
        // Cargar el ítem existente si estamos editando
        if (!$isNew) {
            if (!$table->load((int) $validData['id'])) {
                $this->setError($table->getError());
                return false;
            }
        }

        // Comprobar permisos (core.edit para existentes, core.create para nuevos)
        $assetName = $this->typeAlias . ($isNew ? '' : '.' . $validData['id']);
        if ($isNew) {
            if (!$user->authorise('core.create', $assetName)) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'));
                return false;
            }
        } else {
            if (!$user->authorise('core.edit', $assetName) && !$user->authorise('core.edit.own', $assetName)) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
                return false;
            }
        }

        // Enlazar los datos validados a la tabla
        if (!$table->bind($validData)) {
            $this->setError($table->getError());
            return false;
        }

        // Validar los datos a nivel de tabla (método check() en ItemTable)
        if (!$table->check()) {
            $this->setError($table->getError());
            return false;
        }

        // Guardar los datos
        if (!$table->store(true)) { // El 'true' es para re-indexar assets si es una tabla anidada (no aplica aquí)
            $this->setError($table->getError());
            return false;
        }

        // Actualizar asset_id y reglas de ACL si el formulario las incluyó (campo 'rules')
        if (isset($validData['rules']) && is_array($validData['rules'])) {
            if (!$this->saveAsset($table->id, $validData['rules'])) {
                // El error ya se establece en saveAsset
                return false;
            }
        }

        // Actualizar el ID en el estado del modelo si es un nuevo ítem
        if ($isNew && $table->id) {
            $this->setState($this->getName() . '.id', $table->id);
        }
        
        // Limpiar la caché del componente y global si es necesario
        $this->cleanCache();
        $app->cleanCache(); // Limpieza más general
        
        // Limpiar datos de sesión después de guardar exitosamente
        $app->setUserState($this->typeAlias . '.edit.data', null);

        return true;
    }

    /**
     * Helper method to save the asset rules.
     *
     * @param   int    $pk     The primary key of the item.
     * @param   array  $rules  The rules array from the form.
     *
     * @return  bool  True on success, false on failure.
     * @since   1.0.0
     */
    protected function saveAsset(int $pk, array $rules): bool
    {
        $assetName = $this->typeAlias . '.' . $pk;
        $asset = Table::getInstance('Asset', 'JTable');

        if (!$asset->loadByName($assetName)) {
            // El asset_id debería haber sido creado/actualizado por ItemTable::store()
            // Si no existe, algo falló en la lógica de ItemTable o el asset no se generó.
            // Esto es una comprobación adicional.
            $itemTable = $this->getTable();
            if (!$itemTable->load($pk) || !$itemTable->asset_id) {
                $this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_ASSET_NOT_FOUND', $assetName));
                return false;
            }
            // Si el asset_id existe en la tabla pero no en #__assets, es un problema.
            // Por ahora, asumimos que ItemTable::store() se encargó de crear el asset básico.
            // Aquí solo actualizamos las reglas.
            $asset->load((int) $itemTable->asset_id);
        }
        
        if ($asset->id && !empty($rules)) {
            if (!$asset->save($rules)) { // El método save de JTableAsset se encarga de formatear las reglas
                $this->setError($asset->getError());
                return false;
            }
        }
        return true;
    }


    /**
     * Method to delete one or more records.
     *
     * @param   array  &$pks  An array of primary key values.
     *
     * @return  bool  True if successful, false if an error occurs.
     *
     * @since   1.0.0
     */
    public function delete(array &$pks): bool
    {
        $user  = Factory::getApplication()->getIdentity();
        $table = $this->getTable();

        foreach ($pks as $pk) {
            if (!$table->load((int) $pk)) {
                $this->setError($table->getError());
                return false;
            }

            // Comprobar permiso de borrado
            if (!$user->authorise('core.delete', $this->typeAlias . '.' . (int) $pk)) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
                return false;
            }

            if (!$table->delete((int) $pk)) {
                $this->setError($table->getError());
                return false;
            }
        }
        
        $this->cleanCache();
        Factory::getApplication()->cleanCache();

        return true;
    }
}