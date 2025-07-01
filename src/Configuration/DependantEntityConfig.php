<?php
namespace App\Configuration;

#use Symfony\Component\Config\Definition\Builder\TreeBuilder;
#use Symfony\Component\Config\Definition\ConfigurationInterface;
use App\Entity\Ciudad;
use App\Entity\Modelo;

$php_84 = 8 * 10000 + 4 * 100;

if (PHP_VERSION_ID < $php_84) {

    function array_find(array $array, callable $callback): mixed
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        return null;
    }
}



class DependantEntityConfig #implements ConfigurationInterface
{
    // public function getConfigTreeBuilder(): TreeBuilder
    // {
    //     $treeBuilder = new TreeBuilder('dependant_entity');
    //
    //     // ... add node definitions to the root of the tree
    //     // $treeBuilder->getRootNode()->...
    //
    //     return $treeBuilder;
    // }

    public static $config_bag = null;

    # class
    # config_name
    # form_options:
    #   parent_entity_field
    #   parent_form_field
    #   entity_field
    #   form_field
    # search_options:
    #   search_order_field
    #   search_order_direction
    #   search_callback
    protected static function setup_config() {
        self::$config_bag = [
            [
                'config_name' => 'ciudad_by_provincia',
                'parent_entity_field' => 'provincia',
                'parent_form_field'=> 'provincia',
                'entity_field' => 'ciudad',
                'form_field' => 'ciudad',
                'class' => Ciudad::class,
                'search_order_field' => 'nombre',
                #'attr' => [
                #    'data-sonata-select2' => 'false'
                #]
            ],
            [
                'config_name' => 'form_vehiculo:modelo_by_marca',
                'parent_entity_field' => 'marca',
                'parent_form_field'=> 'marca',
                'entity_field' => 'modelo',
                'form_field' => 'modelo',
                'class' => Modelo::class,
                'search_order_field' => 'nombre',
            ],
        ];
    }

    public static function form_options($config_name) {
        return self::get_config($config_name);
    }

    protected static function load_config() {
        if(null === self::$config_bag) {
            self::setup_config();
        }
    }

    protected static function get_config($config_name) {
        self::load_config();
        $config = array_find(
            self::$config_bag,
            function($value, $key) use ($config_name) {
                return (array_key_exists('config_name', $value)
                && $value['config_name'] == $config_name);
            }
        );
        return $config;
    }


}
