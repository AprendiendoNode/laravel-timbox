<?php
namespace Ralego\Timbox\Traits;

trait Status
{
    use Base;

    public function status($auth,$uuid,$rfc_emisor,$rfc_receptor,$total)
    {
        $ws = $this->startWS('cancelacion');
        if(!$ws->success)
            return (object)['success'   => false,'message'   => $ws->message];
        
        $parametros = array(
            "username" => $auth->username,
            "password" => $auth->password,
            "uuid" => $uuid,
            "rfc_emisor" => $rfc_emisor,
            "rfc_receptor" => $rfc_receptor,
            "total" => $total
        );
        try {
            $respuesta = $ws->cliente->__soapCall("consultar_estatus", $parametros);
            return (object)[
                'success'             => true,
                'message'             => $respuesta->codigo_estatus,
                'es_cancelable'       => $respuesta->es_cancelable,
                'estado'              => $respuesta->estado,
                'estatus_cancelacion' => $respuesta->estatus_cancelacion
            ];
        } catch (\Exception $exception) {
            return (object)[
                'success'   => false,
                'error'     => $exception->getCode(),
                'message'   => $exception->getMessage()
            ];
        }
    }

    public function pendientes($auth,$cer,$key,$rfc_receptor)
    {
        $ws = $this->startWS('cancelacion');
        if(!$ws->success)
            return (object)['success'   => false,'message'   => $ws->message];
        
        $parametros = array(
            "username" => $auth->username,
            "password" => $auth->password,
            "rfc_receptor" => $rfc_receptor,
            "cert_pem" => $cer,
            "llave_pem" => $key,
        );
        try {
            $respuesta = $ws->cliente->__soapCall("consultar_peticiones_pendientes", $parametros);
            return (object)[
                'success'   => true,
                'codigo'    => $respuesta->codestatus,
                'uuids'     => $respuesta->uuids
            ];
        } catch (\Exception $exception) {
            return (object)[
                'success'   => false,
                'error'     => $exception->getCode(),
                'message'   => $exception->getMessage()
            ];
        }
    }

    public function relacionados($auth,$cer,$key,$rfc_receptor,$uuid)
    {
        $ws = $this->startWS('cancelacion');
        if(!$ws->success)
            return (object)['success'   => false,'message'   => $ws->message];
        
        $parametros = array(
            "username" => $auth->username,
            "password" => $auth->password,
            "uuid" => $uuid,
            "rfc_receptor" => $rfc_receptor,
            "cert_pem" => $cer,
            "llave_pem" => $key,
        );
        try {
            $respuesta = $ws->cliente->__soapCall("consultar_documento_relacionado", $parametros);
            return (object)[
                'success'               => true,
                'message'               => $respuesta->resultado,
                'relacionados_padres'   => $respuesta->relacionados_padres,
                'relacionados_hijos'    => $respuesta->relacionados_hijos
            ];
        } catch (\Exception $exception) {
            return (object)[
                'success'   => false,
                'error'     => $exception->getCode(),
                'message'   => $exception->getMessage()
            ];
        }
    }

    public function responder($auth,$cer,$key,$rfc_receptor,$respuestas)
    {
        $ws = $this->startWS('cancelacion');
        if(!$ws->success)
            return (object)['success'   => false,'message'   => $ws->message];
        
        $parametros = array(
            "username" => $auth->username,
            "password" => $auth->password,
            "rfc_receptor" => $rfc_receptor,
            "respuestas" => $respuestas,
            "cert_pem" => $cer,
            "llave_pem" => $key
        );
        try {
            $respuesta = $ws->cliente->__soapCall("procesar_respuesta", $parametros);
            return (object)[
                'success'               => true,
                'folios'               => $respuesta->folios,
            ];
        } catch (\Exception $exception) {
            return (object)[
                'success'   => false,
                'error'     => $exception->getCode(),
                'message'   => $exception->getMessage()
            ];
        }
    }
}