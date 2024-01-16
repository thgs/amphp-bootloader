<?php declare(strict_types=1);

use Amp\Http\Server\Response;

class DelegatedHandler
{
    public function __invoke(int $param1, string $param2): Response
    {
        // as a sample we will var_dump
        ob_start();
        var_dump(func_get_args());
        $output = ob_get_clean();

        return new Response(body: $output);
    }
}
