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
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Text;
use Joomla\Database\Query\QueryInterface; // Para el tipado de retorno de getListQuery
use Joomla\CMS\Helper\ComponentHelper;

/**
 * Items Model for Timeline component (Administrator)
 *
 * @since  1.0.0
 */
class ItemsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @since   1.0.0
     */
    public function __construct(array $config = [])
    {
        // Definir los campos que se pueden filtrar y ordenar en esta vista de lista
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'description', 'a.description',
                'date', 'a.date',
                'state', 'a.state',
                'ordering', 'a.ordering',
                'created', 'a.created',
                'created_by', 'a.created_by', 'author_name', // Usar el alias de la consulta
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string|null  $ordering   An optional ordering field.
     * @param   string|null  $direction  An optional direction (asc|desc).
     *
     * @return  void
     * @since   1.0.0
     */
    protected function populateState(string $ordering = null, string $direction = null): void
    {
        $app = Factory::getApplication();

        // Cargar el estado de los filtros desde la solicitud
        // Filtro de búsqueda
        $search = $app->input->getString('filter_search'); // Joomla 5 usa app->input
        $this->setState('filter.search', $search);

        // Filtro de estado (publicado/despublicado/archivado/etc.)
        $published = $app->input->getString('filter_published');
        $this->setState('filter.published', $published);
        
        // Parámetros del componente (si los necesitas para la consulta o la lógica)
        // $params = ComponentHelper::getParams('com_timeline');
        // $this->setState('params', $params);

        // Llamar al populateState del padre para manejar ordenación, límites, etc.
        // Establecer valores por defecto para ordenación si no se proporcionan
        parent::populateState($ordering ?? 'a.ordering', $direction ?? 'ASC');
    }

    /**
     * Method to get a DboQuery object for retrieving the data set from a database.
     *
     * @return  QueryInterface|null  A DboQuery object to retrieve the data set or null if error.
     * @since   1.0.0
     */
    protected function getListQuery(): ?QueryInterface
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Campos a seleccionar
        $query->select(
            $this->getState(
                'list.select',
                [
                    $db->quoteName('a.id'),
                    $db->quoteName('a.title'),
                    // $db->quoteName('a.description'), // Descomentar si necesitas la descripción en la lista
                    $db->quoteName('a.date'),
                    $db->quoteName('a.state'),
                    $db->quoteName('a.ordering'),
                    $db->quoteName('a.created'),
                    $db->quoteName('a.created_by'),
                    $db->quoteName('a.checked_out'),
                    $db->quoteName('a.checked_out_time')
                ]
            )
        );
        $query->from($db->quoteName('#__timeline_items', 'a'));

        // Join para el nombre del creador (Autor)
        $query->select($db->quoteName('uc.name', 'author_name'))
            ->join('LEFT', $db->quoteName('#__users', 'uc'), $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.created_by'));
        
        // Join para el nombre del editor (si tuvieras campo modified_by)
        // $query->select($db->quoteName('me.name', 'editor_name'))
        //    ->join('LEFT', $db->quoteName('#__users', 'me'), $db->quoteName('me.id') . ' = ' . $db->quoteName('a.modified_by'));

        // Filtrar por estado
        $state = $this->getState('filter.published');
        if (is_numeric($state)) {
            $query->where($db->quoteName('a.state') . ' = ' . (int) $state);
        } elseif ($state === '') {
            // Por defecto, no mostrar ítems archivados o en papelera si el filtro de estado está vacío
            $query->where($db->quoteName('a.state') . ' IN (0, 1)');
        }

        // Filtrar por búsqueda (en título y descripción)
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) { // Búsqueda por ID
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
            } else { // Búsqueda por texto
                $searchTerm = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where(
                    '(' . $db->quoteName('a.title') . ' LIKE ' . $searchTerm .
                    ' OR ' . $db->quoteName('a.description') . ' LIKE ' . $searchTerm . ')'
                );
            }
        }

        // Añadir la ordenación de la lista
        $orderCol  = $this->state->get('list.ordering', 'a.ordering');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        
        // Validar la columna de ordenación para evitar inyección SQL
        // $config['filter_fields'] debe estar definido en el constructor
        if (in_array($orderCol, $this->filter_fields)) {
             $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        } else {
            // Si la columna no es válida, usar la predeterminada para seguridad
            $query->order($db->quoteName('a.ordering') . ' ASC');
        }

        return $query;
    }
}