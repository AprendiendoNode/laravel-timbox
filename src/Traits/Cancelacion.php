<?php
namespace Ralego\Timbox\Traits;

trait Cancelacion
{
    public function cancelar($auth,$rfc_emisor,$cer,$key,$uuids)
    {
        $ws = $this->startWS('cancelacion');
        if(!$ws->success)
            return (object)['success'   => false,'message'   => $ws->message];
               
        $parametros = array(
            "username" => $auth->username,
            "password" => $auth->password,
            "rfc_emisor" => $rfc_emisor,
            "folios" => $uuids,
            "cert_pem" => $cer,
            "llave_pem" => $key,
        );
        try {
            $respuesta = $ws->cliente->__soapCall("cancelar_cfdi", $parametros);
            return (object)[
                'success'   => true,
                'message'   => $respuesta->folios_cancelacion,
                'xml'       => $respuesta->acuse_cancelacion
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
