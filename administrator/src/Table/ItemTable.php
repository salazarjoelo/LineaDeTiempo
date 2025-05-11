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
use Joomla\CMS\User\User;

/**
 * Item Table class for LineaDeTiempo component.
 * Represents a row in the #__lineadetiempo_items table.
 *
 * @since  1.0.0
 */
class ItemTable extends Table
{
    public ?int $id = null;
    public ?int $asset_id = 0;
    public ?string $title = null;
    public ?string $description = null;
    public ?string $date = null;
    public ?int $state = null;
    public ?int $ordering = null;
    public ?int $created_by = null;
    public ?string $created = null;
    public ?int $modified_by = null;
    public ?string $modified = null;
    public ?int $checked_out = null;
    public ?string $checked_out_time = null;

    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__lineadetiempo_items', 'id', $db);
        $this->setColumnAlias('published', 'state');
    }

    public function bind($src, $ignore = []): bool
    {
        if (isset($src['title'])) {
            $src['title'] = trim((string) $src['title']);
        }
        if (isset($src['description'])) {
            $src['description'] = (string) $src['description'];
        }
        // Si necesitas procesar la fecha aquí antes de bind:
        // if (isset($src['date']) && !empty($src['date'])) {
        //    try {
        //        $dateObj = Factory::getDate($src['date'], Factory::getApplication()->get('offset'));
        //        $src['date'] = $dateObj->toSql(true); // toSql(true) para UTC si tu campo es DATETIME y almacenas en UTC
        //    } catch (\Exception $e) {
        //        $this->setError(Text::sprintf('JLIB_DATABASE_ERROR_VALIDATE_DATE_FORMAT', $src['date']));
        //        return false;
        //    }
        // }
        return parent::bind($src, $ignore);
    }

    public function check(): bool
    {
        if (empty($this->title)) {
            $this->setError(Text::_('COM_LINEADETIEMPO_ERROR_TITLE_REQUIRED'));
            return false;
        }
        if (empty($this->date)) {
            $this->setError(Text::_('COM_LINEADETIEMPO_ERROR_DATE_REQUIRED'));
            return false;
        }

        $user = Factory::getApplication()->getIdentity();
        $nowSql = Factory::getDate()->toSql();

        if (empty($this->id)) { // Nuevo ítem
            $this->created    = $nowSql;
            $this->created_by = $user->id;
            if ($this->state === null) {
                $this->state = 0;
            }
            if (empty($this->ordering)) {
                $this->ordering = self::getNextOrder($this->_db->quoteName('state') . ' = ' . (int) $this->state);
            }
        }

        // Siempre establecer/actualizar campos de modificación
        $this->modified   = $nowSql;
        $this->modified_by = $user->id;

        if (empty($this->asset_id)) {
            $this->asset_id = 0;
        }

        return parent::check();
    }

    public function store($updateNulls = false): bool
    {
        $isNew = empty($this->id);

        if (!parent::store($updateNulls)) {
            return false;
        }

        if ($this->id > 0 && $this->asset_id !== -1) {
            $currentAssetId = (int) $this->asset_id;
            $asset = Table::getInstance('Asset', 'JTable', ['dbo' => $this->getDbo()]);

            if ($isNew || $currentAssetId === 0) { // Necesita un nuevo asset_id o crearlo
                $asset->name        = 'com_lineadetiempo.item.' . $this->id;
                $asset->title       = (string) $this->title;
                $asset->rules       = '{}'; // Reglas por defecto vacías para un nuevo asset
                $asset->parent_id   = $this->_getAssetParentId();
                $asset->setLocation($asset->parent_id, 'last-child');

                if (!$asset->check() || !$asset->store()) {
                    $this->setError($asset->getError());
                    return false;
                }

                if ((int) $asset->id !== $currentAssetId) {
                    $this->asset_id = (int) $asset->id;
                    $query = $this->_db->getQuery(true)
                        ->update($this->_tbl)
                        ->set($this->_db->quoteName('asset_id') . ' = ' . $this->asset_id)
                        ->where($this->_db->quoteName($this->_tbl_key) . ' = ' . (int) $this->id);
                    $this->_db->setQuery($query)->execute();
                }
            }
            // La actualización de las reglas del asset (si vienen del formulario)
            // se maneja mejor en el ItemModel después de que este store() sea exitoso.
        }
        return true;
    }

    protected function _getAssetParentId(): int
    {
        $assetsTable = Table::getInstance('Asset', 'JTable', ['dbo' => $this->getDbo()]);
        $componentAssetName = 'com_lineadetiempo';
        $assetsTable->loadByName($componentAssetName);

        if (!$assetsTable->id) { // Si el asset del componente no existe, créalo
            $rootAssetId = (int) $assetsTable->getRootId();
            $assetsTable->id        = 0;
            $assetsTable->name      = $componentAssetName;
            $assetsTable->title     = $componentAssetName;
            $assetsTable->rules     = '{}';
            $assetsTable->parent_id = $rootAssetId;
            $assetsTable->setLocation($rootAssetId, 'last-child');

            if (!$assetsTable->check() || !$assetsTable->store()) {
                $this->setError($assetsTable->getError()); // Loguea el error
                return $rootAssetId; // Devuelve el raíz como fallback
            }
        }
        return (int) $assetsTable->id;
    }
}