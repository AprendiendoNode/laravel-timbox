<?php
namespace Ralego\Timbox\Traits;

trait Timbrado
{
    public function timbrar($auth,$xml)
    {
        $ws = $this->startWS('timbrado');
        if(!$ws->success)
            return (object)['success'   => false,'message'   => $ws->message];

        $parametros = [
            "username"  => $auth->username,
            "password"  => $auth->password,
            "sxml"      => base64_encode($xml),
        ];
        try {
            $respuesta = $ws->cliente->__soapCall("timbrar_cfdi", $parametros);
            return (object)[
                'success'   => true,
                'message'   => 'XML timbrado correctamente',
                'xml'       => $respuesta->xml
            ];        
        } catch (\Exception $exception) {
            return (object)[
                'success'   => false,
                'error'     => $exception->getCode(),
                'message'   => $exception->getMessage()
            ];
        }
    }

    public function sellar($archivoXml, $key){
        //Leer XML
        $xmlDoc = new \DOMDocument();
        $xmlDoc->loadXML($archivoXml);        
        //Cambiar Fecha a actual y guardar en archivo
        //Obtener el lugar de expedicion del xml
        $lugarDeExpedicion = $xmlDoc->firstChild->getAttribute('LugarExpedicion');
        //Consultar la zona horaria segun el lugar de expedición
        $zonaHoraria = zonaHorariaPorCP($lugarDeExpedicion);
        //Establecer la zona horaria 
        date_default_timezone_set($zonaHoraria);
        $date = date('Y-m-d_H:i:s');
        $date = str_replace("_", "T", $date);
        $xmlDoc->firstChild->setAttribute('Fecha', $date);
        $xmlString = $xmlDoc->saveXML();
        //Crear cadena original
        $xslt = new \DOMDocument();
        $xslt->load(__DIR__ .'/../../docs/cadenaoriginal_3_3.xslt');
        $xml = new \DOMDocument;
        $xml->loadXML($xmlString);
        $proc = new \XSLTProcessor;
        @$proc->importStyleSheet($xslt); // attach the xsl rules
        $cadena = $proc->transformToXML($xml);        
        //Firmar cadena y obtener el digest
        openssl_sign($cadena, $digest, $key, OPENSSL_ALGO_SHA256);        
        //Generar Sello
        $sello = base64_encode($digest);
        //Actualizar el sello del XML
        $xmlDoc->firstChild->setAttribute('Sello', $sello);
        $xmlString = $xmlDoc->saveXML(); 
        return $xmlString;  
    }
    
}
