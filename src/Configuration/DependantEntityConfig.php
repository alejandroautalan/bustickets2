<?php
namespace App\Configuration;

#use Symfony\Component\Config\Definition\Builder\TreeBuilder;
#use Symfony\Component\Config\Definition\ConfigurationInterface;
use App\Entity\Ciudad;
use App\Entity\Modelo;
use App\Entity\Vehiculo;
use App\Entity\Parada;


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
                'no_result_msg' => 'Sin resultados',
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
                'no_result_msg' => 'Sin resultados',
            ],
            [
                'config_name' => 'form_servicio:vehiculo_by_transporte',
                'parent_entity_field' => 'transporte',
                'parent_form_field'=> 'transporte',
                'entity_field' => 'vehiculo',
                'form_field' => 'vehiculo',
                'class' => Vehiculo::class,
                'search_order_field' => 'nombre',
                'no_result_msg' => 'Sin resultados',
            ],
            [
                'config_name' => 'form-config-precio:origen-ciudad-by-provincia',
                'parent_entity_field' => 'provincia',
                'parent_form_field'=> 'origen_provincia',
                'entity_field' => 'origen_ciudad',
                'form_field' => 'origen_ciudad',
                'class' => Ciudad::class,
                'search_order_field' => 'nombre',
                'no_result_msg' => 'Sin resultados',
                'required'=>false,
                'label' => 'Ciudad',
            ],
            [
                'config_name' => 'form-config-precio:origen-parada-by-ciudad',
                'parent_entity_field' => 'ciudad',
                'parent_form_field'=> 'origen_ciudad',
                'entity_field' => 'origen_parada',
                'form_field' => 'origen_parada',
                'class' => Parada::class,
                'search_order_field' => 'nombre',
                'no_result_msg' => 'Sin resultados',
                'required'=>false,
                'label' => 'Parada',
            ],
            [
                'config_name' => 'form-config-precio:destino-ciudad-by-provincia',
                'parent_entity_field' => 'provincia',
                'parent_form_field'=> 'destino_provincia',
                'entity_field' => 'destino_ciudad',
                'form_field' => 'destino_ciudad',
                'class' => Ciudad::class,
                'search_order_field' => 'nombre',
                'no_result_msg' => 'Sin resultados',
                'required'=>false,
                'label' => 'Ciudad',
            ],
            [
                'config_name' => 'form-config-precio:destino-parada-by-ciudad',
                'parent_entity_field' => 'ciudad',
                'parent_form_field'=> 'destino_ciudad',
                'entity_field' => 'destino_parada',
                'form_field' => 'destino_parada',
                'class' => Parada::class,
                'search_order_field' => 'nombre',
                'no_result_msg' => 'Sin resultados',
                'required'=>false,
                'label' => 'Parada',
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
