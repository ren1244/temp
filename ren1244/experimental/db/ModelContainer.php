<?php

namespace ren1244\db;

class ModelContainer
{
    private $_pdo=false;
    private $_models=[];
    private $_modelNameSapce;
    private $_pdoGenerator;
    
    public function __construct($pdoGenerator, $modelNameSapce)
    {
        $this->_modelNameSapce=$modelNameSapce;
        $this->_pdoGenerator=$pdoGenerator;
    }
    
    public function addModel($modelName)
    {
        $this->_models[$this->_modelNameSapce.'\\'.$modelName]=false;
    }
    
    public function __get($modelName)
    {
        $fullName=$this->_modelNameSapce.'\\'.$modelName;
        if(!isset($this->_models[$fullName])){
            throw new \Exception("there is no model named $fullName");
        }
        if(!$this->_models[$fullName]){
            $this->_models[$fullName]=new $fullName($this->getPdo());
        }
        return $this->_models[$fullName];
    }
    
    public function getPdo()
    {
        if(!$this->_pdo){
            $this->_pdo=($this->_pdoGenerator)();
        }
        return $this->_pdo;
    }
}