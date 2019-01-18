<?php
namespace Artify\Artify\Contracts\Database;

interface SQLDriverCreatorInterface {
	public function create();
	public function exists();
}
