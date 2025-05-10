<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\Query\QueryInterface;
use Joomla\CMS\Helper\ComponentHelper; // Para obtener parámetros del componente

/**
 * Items Model for Timeline component (Site)
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
        // Campos que podrían usarse para filtrar o buscar en el frontend (si lo implementas)
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'date', 'a.date',
                // No solemos filtrar por 'state' explícitamente en el frontend,
                // sino que directamente seleccionamos los publicados.
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
        $app    = Factory::getApplication();
        $params = $app->getParams(); // Parámetros del ítem de menú o del componente

        // Ordenación por defecto para el frontend
        // Podría ser 'a.date' para una línea de tiempo cronológica
        $defaultOrdering = $params->get('items_ordering', 'a.date');
        $defaultDirection = $params->get('items_ordering_direction', 'DESC'); // Más recientes primero

        // Forzar estado publicado para el frontend
        $this->setState('filter.published', 1); // 1 = Publicado

        // Obtener el límite para la paginación desde los parámetros del menú o del componente
        $limit = $app->input->getInt('limit', $params->get('list_limit', 10));
        $this->setState('list.limit', $limit);
        
        $this->setState('list.start', $app->input->getInt('start', 0));


        parent::populateState($ordering ?? $defaultOrdering, $direction ?? $defaultDirection);
    }

    /**
     * Method to get a DboQuery object for retrieving the data set from a database.
     *
     * @return  QueryInterface|null  A DboQuery object to retrieve the data set or null if error.
     *
     * @since   1.0.0
     */
    protected function getListQuery(): ?QueryInterface
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select(
            $this->getState(
                'list.select',
                [
                    $db->quoteName('a.id'),
                    $db->quoteName('a.title'),
                    $db->quoteName('a.description'),
                    $db->quoteName('a.date'),
                    // No necesitamos 'state' si siempre filtramos por publicado
                    // $db->quoteName('a.ordering'), // Si usas un ordenamiento específico
                    $db->quoteName('a.created_by'),
                    $db->quoteName('uc.name', 'author_name') // Nombre del autor
                ]
            )
        );
        $query->from($db->quoteName('#__timeline_items', 'a'));

        // Join para el nombre del creador (Autor)
        $query->join('LEFT', $db->quoteName('#__users', 'uc'), $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.created_by'));

        // Filtrar por estado publicado (ya establecido en populateState)
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where($db->quoteName('a.state') . ' = ' . (int) $published);
        } else {
            // Por si acaso, forzar a publicado si no está definido
            $query->where($db->quoteName('a.state') . ' = 1');
        }
        
        // Podrías añadir filtros por categoría aquí si implementas categorías
        // $categoryId = $this->getState('filter.category_id');
        // if (is_numeric($categoryId) && $categoryId > 0) {
        //     $query->where($db->quoteName('a.category_id') . ' = ' . (int) $categoryId);
        // }

        // Añadir la ordenación de la lista
        $orderCol  = $this->state->get('list.ordering', 'a.date'); // Orden por defecto
        $orderDirn = $this->state->get('list.direction', 'DESC');
        
        if (in_array($orderCol, $this->filter_fields)) {
             $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        } else {
            $query->order($db->quoteName('a.date') . ' DESC'); // Fallback seguro
        }
        
        return $query;
    }
}