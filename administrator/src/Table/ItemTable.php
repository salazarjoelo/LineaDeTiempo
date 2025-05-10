<?php
declare(strict_types=1);

namespace Salazarjoelo\Component\LineaDeTiempo\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Language\Text;

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
        // CAMBIA #__timeline_items a #__lineadetiempo_items si renombraste la tabla
        parent::__construct('#__lineadetiempo_items', 'id', $db);
        $this->setColumnAlias('published', 'state'); // JTable lo usa para el método publish
    }

    public function bind($src, $ignore = []): bool
    {
        // Puedes añadir lógica de pre-procesamiento aquí
        return parent::bind($src, $ignore);
    }

    public function check(): bool
    {
        if (trim((string) $this->title) === '') {
            $this->setError(Text::_('COM_LINEADETIEMPO_ERROR_TITLE_REQUIRED'));
            return false;
        }
        if (empty($this->date)) {
            $this->setError(Text::_('COM_LINEADETIEMPO_ERROR_DATE_REQUIRED'));
            return false;
        }

        $user = Factory::getApplication()->getIdentity();
        $now  = Factory::getDate()->toSql();

        if (empty($this->id)) {
            $this->created    = $now;
            $this->created_by = $user->id;
            if ($this->state === null) {
                 $this->state = 0; // Por defecto despublicado para nuevos
            }
            if (empty($this->ordering)) {
                // CAMBIA #__timeline_items a #__lineadetiempo_items si es necesario
                $this->ordering = self::getNextOrder($this->_db->quoteName('state') . ' = ' . (int) $this->state);
            }
        }
        $this->modified   = $now;
        $this->modified_by = $user->id;
        
        // Gestión básica de asset_id
        if (empty($this->asset_id)) {
            $this->asset_id = 0; // Asegurar que sea un entero
        }

        return parent::check();
    }

    public function store($updateNulls = false): bool
    {
        $isNew = empty($this->id);
        $result = parent::store($updateNulls);

        if ($result && $this->id > 0) {
            if ($isNew || (int) $this->asset_id === 0) {
                // Crear o actualizar asset. Asset name: com_lineadetiempo.item.ID
                $assetName = 'com_lineadetiempo.item.' . $this->id;
                $asset = Table::getInstance('Asset', 'JTable', ['dbo' => $this->getDbo()]);
                $asset->loadByName($assetName);
                
                $asset->name = $assetName;
                $asset->title = (string) $this->title;
                $asset->rules = $asset->rules ?: '{}'; // Mantener reglas existentes o default
                
                $assetParentId = $this->_getAssetParentId();
                $asset->setLocation($assetParentId, 'last-child');
                
                if (!$asset->check() || !$asset->store()) {
                    $this->setError($asset->getError());
                    return false;
                }
                
                if ((int) $this->asset_id !== (int) $asset->id) {
                    $this->asset_id = (int) $asset->id;
                    // Actualizar la tabla de ítems con el nuevo asset_id
                    $query = $this->_db->getQuery(true)
                        ->update($this->_tbl)
                        ->set($this->_db->quoteName('asset_id') . ' = ' . $this->asset_id)
                        ->where($this->_db->quoteName($this->_tbl_key) . ' = ' . (int) $this->id);
                    $this->_db->setQuery($query)->execute();
                }
            }
        }
        return $result;
    }
    
    protected function _getAssetParentId($table = null, $id = null): int
    {
        $assetsTable = Table::getInstance('Asset', 'JTable', ['dbo' => $this->getDbo()]);
        $assetsTable->loadByName('com_lineadetiempo'); // El asset del componente

        if ($assetsTable->id) {
            return (int) $assetsTable->id;
        }
        return (int) $assetsTable->getRootId(); // Fallback al asset raíz
    }
}