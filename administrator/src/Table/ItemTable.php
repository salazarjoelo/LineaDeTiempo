<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User; // Para campos de auditoría

/**
 * Item Table class for Timeline component.
 *
 * @since  1.0.0
 */
class ItemTable extends Table
{
    /** @var int|null Primary key */
    public ?int $id = null;

    /** @var int|null Foreign key to the #__assets table. */
    public ?int $asset_id = null;

    /** @var string|null */
    public ?string $title = null;

    /** @var string|null */
    public ?string $description = null;

    /** @var string|null DATETIME field */
    public ?string $date = null;

    /** @var int|null 1 = published, 0 = unpublished, 2 = archived, -2 = trashed */
    public ?int $state = null;

    /** @var int|null */
    public ?int $ordering = null;

    /** @var int|null User ID of the creator */
    public ?int $created_by = null;

    /** @var string|null DATETIME field */
    public ?string $created = null;

    /** @var int|null User ID of the modifier */
    public ?int $modified_by = null;

    /** @var string|null DATETIME field */
    public ?string $modified = null;

    /** @var int|null User ID of the user who checked out the item */
    public ?int $checked_out = null;

    /** @var string|null DATETIME field of when the item was checked out */
    public ?string $checked_out_time = null;

    /**
     * Constructor.
     *
     * @param   DatabaseDriver  $db  A DatabaseDriver object.
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__timeline_items', 'id', $db);
        // $this->setColumnAlias('published', 'state'); // Si usaras 'published' en el formulario pero la columna es 'state'
    }

    /**
     * Overloaded bind method to preprocess data.
     *
     * @param   array|object  $src     An associative array or object to bind to the table.
     * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
     *
     * @return  bool  True on success.
     *
     * @since   1.0.0
     */
    public function bind($src, $ignore = []): bool // $src puede ser array u objeto
    {
        // Aquí puedes pre-procesar datos antes de que se enlacen.
        // Por ejemplo, si la fecha viene en un formato diferente y necesita ser convertida.
        // if (isset($src['date']) && !empty($src['date'])) {
        //    try {
        //        $dateObj = Factory::getDate($src['date']);
        //        $src['date'] = $dateObj->toSql();
        //    } catch (\Exception $e) {
        //        $this->setError(Text::sprintf('JLIB_DATABASE_ERROR_VALIDATE_DATE_FORMAT', $src['date']));
        //        return false;
        //    }
        // }

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
        // Validar título (requerido)
        if (trim((string) $this->title) === '') {
            $this->setError(Text::_('COM_TIMELINE_ERROR_TITLE_REQUIRED'));
            return false;
        }
        $this->title = trim((string) $this->title);

        // Validar fecha (requerida)
        if (empty($this->date)) {
            $this->setError(Text::_('COM_TIMELINE_ERROR_DATE_REQUIRED'));
            return false;
        }
        // Aquí podrías añadir validación de formato para la fecha si no se hizo en bind()

        // Descripción (opcional, pero limpiar si se proporciona)
        if ($this->description !== null) {
            $this->description = trim((string) $this->description);
        }
        
        // Estado por defecto si es un nuevo ítem
        if ($this->state === null && empty($this->id)) {
            $this->state = 0; // Por defecto despublicado para nuevos ítems, o 1 si prefieres publicado
        }

        // Campos de auditoría
        $user = Factory::getApplication()->getIdentity();
        $now  = Factory::getDate()->toSql();

        if (empty($this->id)) { // Nuevo ítem
            $this->created    = $now;
            $this->created_by = $user->id;
            // El ordenamiento usualmente se maneja en el modelo o al final de la tabla
            if (empty($this->ordering)) {
                $this->ordering = $this->getNextOrder();
            }
        }
        // Siempre actualizar campos de modificación
        $this->modified   = $now;
        $this->modified_by = $user->id;

        return parent::check();
    }

    /**
     * Method to store a row in the database.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0.0
     */
    public function store($updateNulls = false): bool
    {
        // Lógica para manejar el asset_id si usas ACL a nivel de ítem
        $isNew = empty($this->id);

        if (!parent::store($updateNulls)) {
            return false;
        }

        // Si usas ACL, y es un nuevo ítem, o un ítem sin asset_id, genera el asset_id
        // Esto es una implementación básica. Una completa revisaría reglas, etc.
        if (($isNew || (int) $this->asset_id === 0) && $this->id > 0) {
            $this->asset_id = $this->getAssetId('com_timeline.item', (int) $this->id);
            
            // Actualizar la tabla con el nuevo asset_id
            $query = $this->_db->getQuery(true)
                ->update($this->_tbl)
                ->set($this->_db->quoteName('asset_id') . ' = ' . (int) $this->asset_id)
                ->where($this->_db->quoteName($this->_tbl_key) . ' = ' . (int) $this->id);
            $this->_db->setQuery($query);

            try {
                $this->_db->execute();
            } catch (\RuntimeException $e) {
                $this->setError($e->getMessage());
                return false;
            }
        }

        return true;
    }
    
    /**
     * Method to get the asset_id.
     *
     * @param   string   $name  The name of the asset. Example: com_mycomponent.article.
     * @param   integer  $id    The id of the item.
     *
     * @return  integer  The asset id.
     *
     * @since   1.0.0
     */
    protected function getAssetId(string $name, int $id): int
    {
        $assetTable = Table::getInstance('Asset', 'JTable', ['dbo' => $this->getDbo()]);
        $assetId    = $assetTable->getRootId(); // Default to root asset

        // Get the component's asset ID first
        if ($assetTable->loadByName('com_timeline')) {
            $parentId = $assetTable->id;
        } else {
            $parentId = $assetId; // Fallback to root if component asset not found
        }

        $assetName = $name . '.' . $id;

        if ($assetTable->loadByName($assetName)) {
            $assetId = $assetTable->id;
        } else {
            $assetTable->id          = 0; // Important to reset ID for new asset
            $assetTable->name        = $assetName;
            $assetTable->title       = $assetName; // Or use $this->title if available
            $assetTable->rules       = '{}'; // Default empty rules
            $assetTable->parent_id   = $parentId;
            $assetTable->setLocation($parentId, 'last-child'); // Joomla 4/5 way

            if (!$assetTable->check() || !$assetTable->store()) {
                $this->setError($assetTable->getError());
                return 0; // Indicate error
            }
            $assetId = $assetTable->id;
        }
        return $assetId;
    }
}