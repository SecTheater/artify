<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    protected $connector = 'and', $betweenQuery = null;

    public function __call($method, $arguments)
    {
        if (starts_with($method, 'between')) {
			$finder = substr($method, 7);
			$segments = preg_split(
				'/(And|Or)(?=[A-Z])/', $finder, -1, PREG_SPLIT_DELIM_CAPTURE
			);
			$i = 0;
			foreach ($segments as $segment) {
				$segment = strtolower(snake_case($segment));
				if ($segment == 'or' || $segment == 'and') {
					$connectorIndex = $i;
					$this->connector = $segment;
					continue;
				}
				if (isset($connectorIndex) && $i > $connectorIndex) {
					$this->connector = 'and';
				}
				$this->parameters = count($arguments) == count($arguments, COUNT_RECURSIVE) ? $arguments : $arguments[$i];

				$this->setBetweenQuery($this->chainBetween($segment));
				$i++;
			}
			return $this->getBetweenQuery();
		}
        return $this->model->{$method}(...$arguments);
    }
    protected function between(string $column, $from, $to) {
		return ($this->getBetweenQuery() ?? $this->model)->whereBetween($column, [$from, $to], $this->connector);
	}
	protected function chainBetween($segment) {
		if (!$this->hasBetweenQuery()) {
			$this->setBetweenQuery($this->between($segment, ...$this->parameters));
			return $this->getBetweenQuery();
		}
		return $this->between($segment, ...$this->parameters);
	}

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $record = $this->getById($id);
        $record->update($attributes);
        return $record;
    }

    public function delete($id)
    {
        return $this->getById($id)->delete();
    }
   	public function getBetweenQuery() {
		return $this->betweenQuery;
	}
	public function setBetweenQuery($betweenQuery) {
		$this->betweenQuery = $betweenQuery;
	}
	public function hasBetweenQuery() {
		return !!$this->betweenQuery;
	}

}
