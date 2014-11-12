<?php
namespace nyankod;

class JsonDB
{

    protected $path = "./";
    protected $fileExt = ".json";
    protected $tables = array();

    /**
     * @param null $path
     */
    public function __construct($path = null)
    {
        if ($path !== null) {
            $this->init($path);
        }
    }

    /**
     * @param $path
     */
    public function init($path)
    {
        $this->validatePath($path);
    }

    /**
     * Validate Path
     *
     * @param $path
     *
     * @throws JsonDBException
     */
    private function validatePath($path)
    {
        if (is_dir($path)) {
            if (substr($path, strlen($path) - 1) != '/') {
                $path .= '/';
            }
            $this->path = $path;
        } else {
            throw new JsonDBException('Path not found');
        }
    }

    /**
     * Get Table Instance
     *
     * @param $table
     *
     * @return JsonTable
     */
    protected function getTableInstance($table, $create)
    {
        if (isset($tables[$table])) {
            return $tables[$table];
        } else {
            return $tables[$table] = new JsonTable($this->path . $table, $create);
        }
    }

    /**
     * Call
     *
     * @param mixed $op
     * @param mixed $args
     *
     * @return mixed
     * @throws JsonDBException
     */
    public function __call($op, $args) {
        if ($args && method_exists("nyankod\JsonTable", $op)) {
            $table = $args[0].$this->fileExt;
            $create = true;
            if($op == "createTable")
            {
                return $this->getTableInstance($table, true);
            }
            elseif($op == "insert" && isset($args[2]) && $args[2] === false)
            {
                $create = false;
            }
            return $this->getTableInstance($table, $create)->$op($args);
        } else throw new JsonDBException("JsonDB Error: Unknown method or wrong arguments ");
    }

    /**
     * Set Extension
     *
     * @param $_fileExt
     *
     * @return $this
     */
    public function setExtension($_fileExt)
    {
        $this->fileExt = $_fileExt;

        return $this;
    }

}
