<?php
/**
 * @package     Salazarjoelo\Component\LineaDeTiempo
 * @subpackage  com_lineadetiempo
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\LineaDeTiempo\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User; // Para campos de auditoría

/**
 * Item Table class for LineaDeTiempo component.
 * Represents a row in the #__lineadetiempo_items table.
 *
 * @since  1.0.0
 */
class ItemTable extends Table
{
    // Propiedades de clase deben coincidir con las columnas de tu tabla #__lineadetiempo_items
    public ?int $id = null;
    public ?int $asset_id = 0; // Default a 0, importante para ACL
    public ?string $title = null;
    public ?string $description = null;
    public ?string $date = null; // Formato DATETIME 'YYYY-MM-DD HH:MM:SS'
    public ?int $state = null; // 0 (unpublished), 1 (published), 2 (archived), -2 (trashed)
    public ?int $ordering = null;
    public ?int $created_by = null;
    public ?string $created = null; // Formato DATETIME
    public ?int $modified_by = null;
    public ?string $modified = null; // Formato DATETIME
    public ?int $checked_out = null;
    public ?string $checked_out_time = null; // Formato DATETIME

    /**
     * Constructor.
     *
     * @param   DatabaseDriver  $db  A DatabaseDriver object.
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__lineadetiempo_items', 'id', $db); // Nombre de tu tabla y clave primaria
        $this->setColumnAlias('published', 'state'); // Permite que JTable::publish() funcione con la columna 'state'
    }

    /**
     * Overloaded bind method to preprocess data or filter.
     *
     * @param   array|object  $src     An associative array or object to bind to the table.
     * @param   array|string  $ignore  An optional array or space separated list of properties to ignore.
     *
     * @return  bool  True on success.
     *
     * @since   1.0.0
     */
    public function bind($src, $ignore = []): bool
    {
        // Ejemplo: Si la fecha viene de un formulario en un formato específico,
        // podrías necesitar convertirla a YYYY-MM-DD HH:MM:SS aquí antes de enlazar.
        // if (isset($src['date']) && !empty($src['date'])) {
        //    try {
        //        $dateObj = Factory::getDate($src['date']); // Asume que el formato de entrada es parseable por JDate
        //        $src['date'] = $dateObj->toSql();
        //    } catch (\Exception $e) {
        //        $this->setError(Text::sprintf('JLIB_DATABASE_ERROR_VALIDATE_DATE_FORMAT', $src['date']));
        //        return false;
        //    }
        // }

        // Limpiar el título
        if (isset($src['title'])) {
            $src['title'] = trim((string) $src['title']);
        }

        // Limpiar descripción (HTML se permite si el campo del formulario es 'editor' y el filtro es 'safehtml' o similar)
        if (isset($src['description'])) {
            // La limpieza real del HTML (si es de un editor) se hace mejor con el filtro del campo del formulario.
            // Aquí solo nos aseguramos de que sea un string.
            $src['description'] = (string) $src['description'];
        }

        return parent::bind($src, $ignore);
    }

    /**
     * Overloaded check method to validate data.
     *
     * @return  bool  True if the data is valid, false otherwise.
     *
     * @since   1.0.0
     */
    public function check(): bool
    {
        // Verificar título (requerido)
        if (empty($this->title)) {
            $this->setError(Text::_('COM_LINEADETIEMPO_ERROR_TITLE_REQUIRED'));
            return false;
        }

        // Verificar fecha (requerida)
        if (empty($this->date)) {
            $this->setError(Text::_('COM_LINEADETIEMPO_ERROR_DATE_REQUIRED'));
            return false;
        }
        // Aquí podrías añadir validación más estricta del formato de fecha si es necesario,
        // aunque el campo 'calendar' del formulario debería ayudar con esto.

        // Estado por defecto si es un nuevo ítem y no se ha establecido
        if ($this->state === null && empty($this->id)) {
            $this->state = 0; // Por defecto despublicado (o 1 si prefieres publicado por defecto)
        }

        // Campos de Auditoría
        $user = Factory::getApplication()->getIdentity();
        $dateHelper = Factory::getDate(); // Para obtener la fecha actual en formato SQL

        if (empty($this->id)) { // Es un nuevo ítem
            $this->created    = $dateHelper->toSql();
            $this->created_by = $user->id;

            // Establecer el orden si no se ha proporcionado
            if (empty($this->ordering)) {
                // Condición para getNextOrder, por ejemplo, si tienes categorías, sería dentro de una categoría.
                // Por ahora, asumimos un ordenamiento general.
                $condition = ''; // Ej: $this->_db->quoteName('category_id') . ' = ' . (int) $this->category_id;
                $this->ordering = $this->getNextOrder($condition);
            }
        }

        // Siempre actualizar campos de modificación (o establecerlos si es nuevo)
        $this->modified   = $dateHelper->toSql();
        $this->modified_by = $user->id;
        
        // Inicializar asset_id si está vacío (importante para nuevos ítems antes del primer store)
        if (empty($this->asset_id)) {
            $this->asset_id = 0;
        }

        return parent::check(); // Llama a las validaciones de la clase Table padre si existen
    }

    /**
     * Method to store a row in the database.
     * Handles asset creation/update for ACL.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0.0
     */
    public function store($updateNulls = false): bool
    {
        $isNew = empty($this->id);

        // Intentar guardar los datos principales
        if (!parent::store($updateNulls)) {
            return false;
        }

        // Si se guardó correctamente y tenemos un ID (sea nuevo o existente)
        // y si el sistema de assets está activo (asset_id != -1, que significa no usar assets)
        if ($this->id > 0 && $this->asset_id !== -1) {
            $asset = Table::getInstance('Asset', 'JTable', ['dbo' => $this->getDbo()]);
            $assetId = (int) $this->asset_id; // El asset_id actual del ítem

            // Si es un nuevo ítem o no tenía asset_id, necesitamos uno nuevo
            if ($isNew || $assetId === 0) {
                $asset->name        = 'com_lineadetiempo.item.' . $this->id;
                $asset->title       = (string) $this->title;
                $asset->rules       = '{}'; // Reglas por defecto vacías
                $asset->parent_id   = $this->_getAssetParentId(); // Obtener el asset padre
                $asset->setLocation($asset->parent_id, 'last-child');

                if (!$asset->check() || !$asset->store()) {
                    $this->setError($asset->getError());
                    return false;
                }
                
                // Si el asset_id se generó y es diferente al que teníamos (o era 0), actualizar la tabla del ítem
                if ($asset->id > 0 && $asset->id !== $assetId) {
                    $this->asset_id = (int) $asset->id;
                    $query = $this->_db->getQuery(true)
                        ->update($this->_tbl)
                        ->set($this->_db->quoteName('asset_id') . ' = ' . $this->asset_id)
                        ->where($this->_db->quoteName($this->_tbl_key) . ' = ' . (int) $this->id);
                    $this->_db->setQuery($query);

                    try {
                        $this->_db->execute();
                    } catch (\RuntimeException $e) {
                        $this->setError($e->getMessage());
                        return false;
                    }
                }
            }
            // Nota: Si las reglas ACL vienen del formulario (campo 'rules'),
            // el modelo (ItemModel) usualmente se encarga de guardarlas
            // después de que este método store() haya sido exitoso y el asset_id esté establecido.
        }

        return true;
    }

    /**
     * Method to get the asset parent ID.
     *
     * @return  int  The asset parent ID.
     * @since   1.0.0
     */
    protected function _getAssetParentId(): int
    {
        $assetsTable = Table::getInstance('Asset', 'JTable', ['dbo' => $this->getDbo()]);
        $componentAssetLoaded = $assetsTable->loadByName('com_lineadetiempo');

        if (!$componentAssetLoaded || !$assetsTable->id) {
            // Si el asset del componente no existe, intenta crearlo bajo el asset raíz.
            $rootAssetId = (int) $assetsTable->getRootId();
            $assetsTable->id        = 0; // Reset para nuevo registro
            $assetsTable->name      = 'com_lineadetiempo';
            $assetsTable->title     = 'com_lineadetiempo'; // O un título más descriptivo
            $assetsTable->rules     = '{}';
            $assetsTable->parent_id = $rootAssetId;
            $assetsTable->setLocation($rootAssetId, 'last-child');

            if (!$assetsTable->check() || !$assetsTable->store()) {
                // No se pudo crear el asset del componente, usar el raíz como fallback
                $this->setError($assetsTable->getError());
                return $rootAssetId;
            }
        }
        return (int) $assetsTable->id;
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database.
     * La clase Table base de Joomla ya tiene un método publish(),
     * pero si necesitas lógica adicional, puedes sobrescribirlo.
     * Asegúrate de que $this->setColumnAlias('published', 'state'); esté en el constructor.
     *
     * @param   mixed    $pks    An optional array of primary key values to update. If not set the instance property value is used.
     * @param   integer  $state  The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer  $userId The user ID of the user performing the operation.
     *
     * @return  boolean  True on success.
     * @since   1.0.0
     */
    // public function publish($pks = null, int $state = 1, int $userId = 0): bool
    // {
    //     // $userId = $userId ?: Factory::getApplication()->getIdentity()->id;
    //     // Lógica personalizada aquí si es necesario
    //     return parent::publish($pks, $state, $userId);
    // }
}