<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Site\View\Items; // Namespace correcto para la vista del frontend

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ComponentHelper; // Para parámetros
use Joomla\CMS\Pagination\Pagination; // Para tipado

/**
 * Items View for Timeline component (Site)
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected ?array $items = null;
    protected ?Pagination $pagination = null;
    protected ?object $state = null;
    protected ?object $params = null; // Parámetros del componente o del ítem de menú

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse.
     *
     * @return  void
     * @since   1.0.0
     */
    public function display(string $tpl = null): void
    {
        $app = Factory::getApplication();

        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->params        = $app->getParams(); // Obtener parámetros (del menú o globales del componente)

        // Verificar errores
        if (count($errors = $this->get('Errors'))) {
            // En el frontend, podrías querer mostrar un mensaje de error amigable
            // o registrar el error y mostrar una página genérica.
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            // Podrías usar JError::raiseWarning o JError::raiseError en J3, pero en J5 es mejor enqueueMessage
            // o lanzar una excepción que tu plantilla de error maneje.
            return;
        }

        // Establecer el título de la página del navegador
        $title = $this->params->get('page_title', Text::_('COM_TIMELINE_DEFAULT_PAGE_TITLE'));
        if ($app->getMenu()->getActive() == $app->getMenu()->getDefault()) { // Si es la página de inicio
            // No establecer título o uno específico para la home
        } elseif ($title) {
            $this->document->setTitle($title);
        }

        // Añadir metadatos si es necesario
        // $this->document->setDescription($this->params->get('menu-meta_description'));
        // $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));

        // Cargar CSS/JS del frontend (ejemplo)
        // $wa = $this->document->getWebAssetManager();
        // $wa->registerAndUseStyle('com_timeline.frontend.list', 'com_timeline/frontend-list.css'); // Asume media/com_timeline/css/frontend-list.css
        // $wa->registerAndUseScript('com_timeline.frontend.list', 'com_timeline/frontend-list.js'); // Asume media/com_timeline/js/frontend-list.js

        parent::display($tpl);
    }
}