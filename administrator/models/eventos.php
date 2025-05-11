<?php
/**
 * @package     LineaDeTiempo
 * @subpackage  com_lineadetiempo
 * @author      Joel Salazar <salazarjoelo@gmail.com>
 * @license     GNU General Public License v3+
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class LineadetiempoModelEventos extends ListModel
{
    /**
     * Constructor
     * @param   array  $config  An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        $config['filter_fields'] = array(
            'id', 'a.id',
            'titulo', 'a.titulo',
            'published', 'a.published',
            'ordering', 'a.ordering',
            'red_social', 'a.red_social',
            'created_by', 'a.created_by',
            'fecha', 'a.fecha'
        );

        parent::__construct($config);
    }

    /**
     * Build an SQL query to load the list data.
     * @return  \Joomla\Database\DatabaseQuery
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__lineadetiempo_eventos', 'a'));

        // Filtro: Estado de publicación
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where($db->quoteName('a.published') . ' = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(' . $db->quoteName('a.published') . ' IN (0, 1))');
        }

        // Filtro: Búsqueda
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(' . $db->quoteName('a.titulo') . ' LIKE ' . $search . 
                   ' OR ' . $db->quoteName('a.descripcion') . ' LIKE ' . $search . ')');
        }

        // Filtro: Red social
        $redSocial = $this->getState('filter.red_social');
        if (!empty($redSocial)) {
            $query->where($db->quoteName('a.red_social') . ' = ' . $db->quote($redSocial));
        }

        // Ordenamiento
        $orderCol = $this->state->get('list.ordering', 'a.ordering');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    /**
     * Save the reordered items
     * @param   array    $idArray    Array of item IDs
     * @param   array    $orderArray Array of order values
     * @return  boolean  True on success
     */
    public function saveorder($idArray = null, $orderArray = null)
    {
        try {
            $table = $this->getTable();
            $table->reorder(); // Resetear valores de ordenamiento
            
            // Actualizar con nuevo orden
            foreach ($orderArray as $position => $id) {
                $table->load((int) $id);
                $table->ordering = $position + 1;
                $table->store();
            }
            
            return true;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Populate the model state
     * @param   string  $ordering   Optional ordering field
     * @param   string  $direction  Optional ordering direction
     * @return  void
     */
    protected function populateState($ordering = 'a.ordering', $direction = 'ASC')
    {
        // Inicializar variables
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $redSocial = $this->getUserStateFromRequest($this->context . '.filter.red_social', 'filter_red_social', '');
        $this->setState('filter.red_social', $redSocial);

        parent::populateState($ordering, $direction);
    }

    /**
     * Get the table object
     * @param   string  $name     Table name
     * @param   string  $prefix   Table prefix
     * @param   array   $options  Configuration array
     * @return  \Joomla\CMS\Table\Table
     */
    public function getTable($name = 'Evento', $prefix = 'LineadetiempoTable', $options = array())
    {
        return parent::getTable($name, $prefix, $options);
    }
}