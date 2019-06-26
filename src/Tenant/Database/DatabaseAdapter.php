<?php
namespace Artify\Artify\Tenant\Database;

use Artify\Artify\Contracts\Database\DatabaseAdapterInterface;
use Artify\Artify\Contracts\Models\Tenant;

class DatabaseAdapter implements DatabaseAdapterInterface
{
    protected $connections = ['pgsql', 'sqlite', 'mysql'];
    protected $connection = null;
    public function __construct($connection)
    {
        $this->connection = $connection;
    }
    public function make($tenant = null)
    {
        if ($this->inConnections()) {
            return $this->getConnectionInstance($tenant);
        }
        throw new \Exception('Invalid Connection Supplied');
    }

    protected function getConnectionInstance($tenant = null)
    {
        $connection = 'Artify\\Artify\\Tenant\\Database\\' . $this->trasnformConnection();
        return new $connection($tenant ?? app(Tenant::class));
    }
    protected function inConnections()
    {
        return in_array($this->connection, $this->getConnections());
    }
    protected function actualConnectionDriversName()
    {
        return [
            'Postgres', 'SQLite', 'MySQL',
        ];
    }
    protected function trasnformConnection()
    {
        return str_replace($this->connections, $this->actualConnectionDriversName(), $this->connection) . 'Creator';
    }
    public function getConnections()
    {
        return $this->connections;
    }
    public function setConnections(array $connections)
    {
        $this->connections = $connections;
    }
}
