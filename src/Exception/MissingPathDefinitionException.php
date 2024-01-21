<?php declare(strict_types=1);

namespace thgs\Bootstrap\Exception;

use thgs\Bootstrap\Config\Route\Fallback;
use thgs\Bootstrap\Config\Route\Path;

class MissingPathDefinitionException extends \Exception
{
    public function __construct(Path|Fallback $forRoute)
    {
        if ($forRoute instanceof Path) {
            $type = 'Path';
            $route = $forRoute->method . ' ' . $forRoute->uri;
        } else {
            $type = 'Fallback';
            $route = '';
        }
        parent::__construct(\sprintf('Missing path definition for %s route %s', $type, $route));
    }
}
