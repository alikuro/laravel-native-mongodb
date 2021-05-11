<?php

namespace Alikuro\Mongodb;

use Illuminate\Support\Facades\Facade;

class MongodbFacade extends Facade {

	protected static function getFacadeAccessor() {
		return 'Alikuro\Mongodb\Query';
	}

}
