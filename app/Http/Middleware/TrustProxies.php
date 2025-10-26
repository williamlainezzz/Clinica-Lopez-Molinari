<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * IPs o CIDRs de proxies a confiar. Deja '*' si estás en local
     * o configúralo según tu infraestructura.
     */
    protected $proxies = '*';

    /**
     * Encabezados usados para detectar el cliente original.
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR
                        | Request::HEADER_X_FORWARDED_HOST
                        | Request::HEADER_X_FORWARDED_PORT
                        | Request::HEADER_X_FORWARDED_PROTO
                        | Request::HEADER_X_FORWARDED_AWS_ELB;
}
