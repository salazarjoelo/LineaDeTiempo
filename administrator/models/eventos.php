<?php
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

class LineadetiempoModelEventos extends ListModel
{
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
        
        // Inyectar dependencia de base de datos
        $this->setDbo(Factory::getContainer()->get('DatabaseDriver'));
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('a.*, u.name AS author')
            ->from($db->quoteName('#__lineadetiempo_eventos', 'a'))
            ->leftJoin(
                $db->quoteName('#__users', 'u') . 
                ' ON ' . $db->quoteName('a.created_by') . ' = ' . $db->quoteName('u.id')
            );

        // Filtros dinámicos
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where($db->quoteName('a.published') . ' = ' . (int) $published);
        }

        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(
                a.titulo LIKE ' . $search . ' 
                OR a.descripcion LIKE ' . $search . '
                OR u.name LIKE ' . $search . '
            )');
        }

        // Ordenamiento profesional
        $orderCol = $this->state->get('list.ordering', 'a.ordering');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        
        if ($orderCol == 'a.ordering') {
            $query->order($db->escape('a.ordering ' . $orderDirn));
        } else {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function saveorder($pks = null, $order = null)
    {
        try {
            $db = $this->getDbo();
            $table = $this->getTable();
            
            // Transacción para integridad
            $db->transactionStart();
            
            foreach ($order as $i => $pk) {
                $table->load((int) $pk);
                $table->ordering = $i + 1;
                
                if (!$table->store()) {
                    throw new RuntimeException($table->getError());
                }
            }
            
            $db->transactionCommit();
            return true;
            
        } catch (Exception $e) {
            $db->transactionRollback();
            $this->setError($e->getMessage());
            return false;
        }
    }
}