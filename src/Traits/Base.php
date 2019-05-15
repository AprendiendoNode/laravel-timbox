<?php
namespace Ralego\Timbox\Traits;

trait Base
{
    public function startWS($type)
    {
        if($type === 'timbrado'){
            if(env('APP_ENV') === 'production')
                $wsdl_url = 'https://sistema.timbox.com.mx/timbrado_cfdi33/wsdl';
            else
                $wsdl_url = 'https://staging.ws.timbox.com.mx/timbrado_cfdi33/wsdl';
        }else{
            if(env('APP_ENV') === 'production')
                $wsdl_url = 'https://sistema.timbox.com.mx/cancelacion/wsdl';
            else
                $wsdl_url = 'https://staging.ws.timbox.com.mx/cancelacion/wsdl';
        }

        if(!strpos(@get_headers($wsdl_url)[0],'200') === false ? false : true)
            return (object)[
                'success'   => false,
                'message'   => 'Servicio de ' . $type . ' no disponible'
            ];
        //  Crear un cliente para hacer la petición al WS
        $cliente = new \SoapClient($wsdl_url, [
            'trace' => 1, 
            'use' => SOAP_LITERAL
        ]);

        return (object)[
            'success'   => true,
            'cliente'   => $cliente
        ];
    }
}