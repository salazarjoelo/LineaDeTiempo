<?php
/**
 * @package     Salazarjoelo\Component\LineaDeTiempo
 * @subpackage  com_lineadetiempo
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\LineaDeTiempo\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Application\CMSApplication; // Aunque no se use directamente, es parte de la firma del constructor padre
use Joomla\Input\Input; // Aunque no se use directamente, es parte de la firma del constructor padre
use Joomla\CMS\MVC\Model\BaseDatabaseModel; // Para el tipado de getModel si se sobrescribe

/**
 * Items list controller class for LineaDeTiempo component (Administrator).
 *
 * @since  1.0.0
 */
class ItemsController extends AdminController
{
    /**
     * The prefix for controller messages.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $text_prefix = 'COM_LINEADETIEMPO'; // Asegura que los mensajes usen este prefijo

    /**
     * Constructor.
     *
     * @param   array                $config   An optional associative array of configuration settings.
     * @param   ?MVCFactoryInterface $factory  The factory.
     * @param   ?CMSApplication      $app      The Application object.
     * @param   ?Input               $input    Input object.
     *
     * @since   1.0.0
     */
    public function __construct(array $config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        parent::__construct($config, $factory, $app, $input);
        // AdminController ya registra tareas estándar como publish, unpublish, archive, trash, delete, checkin.
        // No necesitas registrarlas aquí a menos que la lógica sea muy diferente.
    }

    /**
     * Proxy for getModel.
     * Asegura que se cargue el modelo 'Items' (plural) para las operaciones de lista.
     *
     * @param   string  $name    The name of the model.
     * @param   string  $prefix  The prefix for the class name.
     * @param   array   $config  Configuration array for model.
     *
     * @return  BaseDatabaseModel|false  Model object on success; otherwise false.
     *
     * @since   1.0.0
     */
    public function getModel(string $name = 'Items', string $prefix = 'Administrator', array $config = []): BaseDatabaseModel|false
    {
        // El nombre 'Items' hará que MVCFactory busque la clase ItemsModel
        // en el namespace Salazarjoelo\Component\LineaDeTiempo\Administrator\Model
        return parent::getModel($name, $prefix, $config);
    }
}