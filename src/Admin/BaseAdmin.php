<?php
declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class BaseAdmin extends Admin {
    # List contexts
    protected $contexts = null;
    
    protected $disable_batch_actions = False;

    protected $token_storage = null;

    public function setTokenStorage($token_storage) {
        $this->token_storage = $token_storage;
    }
    
    public function getUser() {
        $user = $this->token_storage->getToken()->getUser();
        return $user;
    }


    public function getEntityManager($class) {
        return $this->getModelManager()
        ->getEntityManager($class)
        ;
    }
    
    public function getEntityRepository($class) {
        return $this->getModelManager()
              ->getEntityManager($class)
              ->getRepository($class)
              ;
    }
    
    public function getAuthChecker() {
        $container = $this->getConfigurationPool()->getContainer();
        $checker = $container->get('security.authorization_checker');
        return $checker;
    }
    
    public function getExportFormats(): array
    {
        return [
            'csv', 'xls',
        ];
    }

/*

    # Note: replaced by configureBatchActions
    public function getBatchActions() {
    if($this->disable_batch_actions == True)
        return array();

        $actions = parent::getBatchActions();

    return $actions;
    }

    protected function configureExportFields(): array
    {
        //$fields
        $ret = array();
        $list = $this->getList();

        $excluded_columns = array("batch","_action");

        foreach($list->getElements() as $k=>$v){
            if(!in_array($k,$excluded_columns)){
                $ret[] = $k;
            }
        }

        return $ret;
    }
    
    public function getDataSourceIterator(): \Iterator {
        $datasourceit = parent::getDataSourceIterator();
        // cambiar formato de fecha en archivo exportado
        $datasourceit->setDateTimeFormat('d/m/Y');
        return $datasourceit;
    }
*/
    
    #
    # List context management functions
    #
    public function getContexts() {
        if($this->contexts === null) {
            $this->contexts = []; // array('list');
        }
        return $this->contexts;
    }
    
    protected function getDefaultContext() {
        $c = $this->getContexts();
        $default = '';
        if(!empty($c)) {
            $default = $c[0];
        }
        return $default;
    }
    
    public function getCurrentContext() {
        $pp = $this->getPersistentParameters();
        return $pp['context'];
    }
    
    protected function configurePersistentParameters(): array
    {
        $parameters = parent::configurePersistentParameters();
        if (!$this->hasRequest()) {
            return $parameters;
        }
        return array_merge($parameters, array(
            'context' => $this->getRequest()->get('context', $this->getDefaultContext()),
        ));
    }

}
