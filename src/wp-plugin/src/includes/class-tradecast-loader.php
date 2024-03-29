<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://www.kiener.nl
 * @since      1.0.0
 *
 * @package    Tradecast
 * @subpackage Tradecast/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Tradecast
 * @subpackage Tradecast/includes
 * @author     Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Loader
{
    /**
     * @var array $actions
     *
     * @since 1.0.0
     * @access protected
     */
    protected $actions;

    /**
     * @var array $filters
     *
     * @since 1.0.0
     * @access protected
     */
    protected $filters;

    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct()
    {
        $this->actions = [];
        $this->filters = [];
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @param string $hook                      The name of the WordPress action that is being registered.
     * @param object $component                 A reference to the instance of the object on which the action is defined.
     * @param string|array<string> $callbacks   The name (or an array of names) of the function definition on the $component.
     * @param int $priority                     Optional. The priority at which the function should be fired. Default is 10.
     * @param int $accepted_args                Optional. The number of arguments that should be passed to the $callback. Default is 1.
     *
     * @since 1.0.0
     * @access public
     */
    public function add_action($hook, $component, $callbacks, $priority = 10, $accepted_args = 1)
    {
        if (is_string($callbacks)) {
            $callbacks = [$callbacks];
        }

        foreach ($callbacks as $callback) {
            $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
        }
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @param string $hook                      The name of the WordPress filter that is being registered.
     * @param object $component                 A reference to the instance of the object on which the filter is defined.
     * @param string|array<string> $callbacks   The name of the function definition on the $component.
     * @param int $priority                     Optional. The priority at which the function should be fired. Default is 10.
     * @param int $accepted_args                Optional. The number of arguments that should be passed to the $callback. Default is 1
     *
     * @since 1.0.0
     * @access public
     */
    public function add_filter($hook, $component, $callbacks, $priority = 10, $accepted_args = 1)
    {
        if (is_string($callbacks)) {
            $callbacks = [$callbacks];
        }

        foreach ($callbacks as $callback) {
            $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
        }
    }

    /**
     * Registers the filters and actions with WordPress.
     *
     * @since 1.0.0
     * @access public
     */
    public function run()
    {
        foreach ($this->filters as $hook) {
            add_filter(
                $hook['hook'],
                [$hook['component'], $hook['callback']],
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        foreach ($this->actions as $hook) {
            add_action(
                $hook['hook'],
                [$hook['component'], $hook['callback']],
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @param array  $hooks             The collection of hooks that is being registered (that is, actions or filters).
     * @param string $hook              The name of the WordPress filter that is being registered.
     * @param object $component         A reference to the instance of the object on which the filter is defined.
     * @param string $callback          The name of the function definition on the $component.
     * @param int $priority             The priority at which the function should be fired.
     * @param int $accepted_args        The number of arguments that should be passed to the $callback.
     * @return array                    The collection of actions and filters registered with WordPress.
     *
     * @since 1.0.0
     * @access private
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args)
    {
        $hooks[] = [
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args,
        ];

        return $hooks;
    }
}
