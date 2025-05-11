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
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Text;
use Joomla\Database\Query\QueryInterface;
use Joomla\CMS\Helper\ComponentHelper;

/**
 * Items Model for LineaDeTiempo component (Administrator)
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
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                // 'description', 'a.description', // Descomenta si quieres buscar/ordenar por descripción
                'date', 'a.date',
                'state', 'a.state',
                'ordering', 'a.ordering',
                'created', 'a.created',
                'created_by', 'a.created_by', 'author_name', // Alias de la consulta
                // Añade más campos si son necesarios para filtrar u ordenar
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
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

        // Filtro de búsqueda
        $search = $app->input->getString('filter_search');
        $this->setState('filter.search', $search);

        // Filtro de estado (publicado/despublicado/archivado/etc.)
        // Tu J3 filtraba por 'published', que es 'state'
        $published = $app->input->getString('filter_state'); // El filtro estándar de Joomla usa filter_state
        $this->setState('filter.published', $published); // Mantenemos 'filter.published' por consistencia con el código anterior

        // Parámetros del componente
        // $params = ComponentHelper::getParams('com_lineadetiempo');
        // $this->setState('params', $params);

        // Ordenación: Tu J3 usaba 'a.ordering ASC' por defecto.
        parent::populateState($ordering ?? 'a.ordering', $direction ?? 'ASC');
    }

    /**
     * Method to get a DboQuery object for retrieving the data set from a database.
     *
     * @return  QueryInterface|null  A DboQuery object or null if error.
     * @since   1.0.0
     */
    protected function getListQuery(): ?QueryInterface
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Campos a seleccionar (basado en tu J3 Model y la tabla)
        $query->select(
            $this->getState(
                'list.select',
                [
                    $db->quoteName('a.id'),
                    $db->quoteName('a.title'),
                    $db->quoteName('a.description'), // Añadido por si lo necesitas en el futuro en la lista
                    $db->quoteName('a.date'),
                    $db->quoteName('a.state'),
                    $db->quoteName('a.ordering'),
                    $db->quoteName('a.created'),
                    $db->quoteName('a.created_by'),
                    $db->quoteName('a.checked_out'),
                    $db->quoteName('a.checked_out_time'),
                    $db->quoteName('uc.name', 'author_name') // Nombre del creador
                ]
            )
        );
        // CAMBIA #__timeline_items a #__lineadetiempo_items si renombraste la tabla
        $query->from($db->quoteName('#__lineadetiempo_items', 'a'));

        // Join para el nombre del creador (Autor) - Tu J3 lo llamaba 'created_by_name'
        $query->join('LEFT', $db->quoteName('#__users', 'uc'), $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.created_by'));

        // Filtrar por estado (publicado, despublicado, archivado)
        $state = $this->getState('filter.published'); // Coincide con el filtro J3
        if (is_numeric($state)) {
            $query->where($db->quoteName('a.state') . ' = ' . (int) $state);
        } elseif ($state === '') { // Si el filtro de estado está "todos" o vacío
            // Tu J3 no filtraba por defecto para mostrar solo publicados, sino que el filtro lo hacía.
            // Para Joomla 5, es común no mostrar archivados (-2) o papelera (-2) a menos que se pida.
            $query->where($db->quoteName('a.state') . ' IN (0, 1, 2)'); // Publicado, Despublicado, Archivado
        }


        // Filtrar por búsqueda (en título, como en tu J3)
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
            } else {
                $searchTerm = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where($db->quoteName('a.title') . ' LIKE ' . $searchTerm);
                // Si quieres buscar también en descripción:
                // $query->orWhere($db->quoteName('a.description') . ' LIKE ' . $searchTerm);
            }
        }

        // Añadir la ordenación de la lista
        $orderCol  = $this->state->get('list.ordering', 'a.ordering'); // Default de J3
        $orderDirn = $this->state->get('list.direction', 'ASC');    // Default de J3
        
        if (in_array($orderCol, $this->filter_fields)) {
             $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        } else {
            // Fallback seguro si la columna de ordenación no es válida
            $query->order($db->quoteName('a.ordering') . ' ASC');
        }

        return $query;
    }
}