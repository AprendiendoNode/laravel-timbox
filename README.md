# Laravel Timbox

* [Instalación](#instalación)
* [Uso](#uso)

Este paquete te ayuda a utilizar todos los servicios de [Timbox](https://www.timbox.com.mx/) de una manera sencilla.

## Instalación

Este paquete puede ser utlizado en Laravel 5.5 o superior.

Instalar el paquete a través de composer:

``` bash
composer require ralego/laravel-timbox
```

## Uso

### Timbrado

Agrega `Ralego\Timbox\Traits\Timbrado` trait en la clase que vayas a necesitar timbrar:

```php
use Ralego\Timbox\Traits\Timbrado;

class HomeController extends Controller
{
    use Timbrado,

    // ...
}
```
Ejemplo:

```php
use Illuminate\Http\Request;
use Ralego\Timbox\Traits\Timbrado;

class HomeController extends Controller
{
    use Timbrado;

    public function timbrar()
    {
        // Cargamos el XML de prueba
        $xml = file_get_contents('ejemplo_cfdi33.xml');
        // Usuario y contraseña de la cuenta de timbox
        $credenciales = (object)[
            'username'  => 'AAA010101000',
            'password'  => 'h6584D56fVdBbSmmnB'
        ];
        // Cargamos la llave del SAT en formato PEM
        $key = file_get_contents('CSD01_AAA010101AAA.key.pem');
        // Sellamos y actualizamos la hora del cfdi versión 3.3.
        $xml = $this->sellar($xml, $key);
        // Timbramos el documento
        $resultado = $this->timbrar($credenciales, $xml);
        // Si ocurre algun error retornamos el mensaje con status 422
        if(!$resultado->success)
            return response($resultado->message, 422);
        
        $xml_timbrado = $resultado->xml;
        return $xml_timbrado;     
    }
}
```

### Cancelacion

Agrega `Ralego\Timbox\Traits\Cancelacion` trait en la clase que vayas a necesitar cancelar:

```php
use Ralego\Timbox\Traits\Cancelacion;

class HomeController extends Controller
{
    use Cancelacion,

    // ...
}
```
Ejemplo:

```php
use Illuminate\Http\Request;
use Ralego\Timbox\Traits\Cancelacion;

class HomeController extends Controller
{
    use Cancelacion;

    public function cancelar()
    {
        // Usuario y contraseña de la cuenta de timbox
        $credenciales = (object)[
            'username'  => 'AAA010101000',
            'password'  => 'h6584D56fVdBbSmmnB'
        ];
        // Cargamos el certificado y la llave del SAT en formato PEM
        $cer = file_get_contents('CSD01_AAA010101AAA.cer.pem');
        $key = file_get_contents('CSD01_AAA010101AAA.key.pem');
        // Colocamos los cfdis a cancelar
        $uuids = [
            [
                "uuid"          => 'EE728194-B356-4B84-9349-254F532A3E01',
                "rfc_receptor"  => 'IAD121214B34',
                "total"         => '1751.60'
            ]// ... n folio a cancelar           
        ];
        $resultado = $this->cancelar($credenciales, 'AAA010101AAA',$cer, $key, $uuids);
        // Si ocurre algun error retornamos el mensaje con status 422
        if(!$resultado->success)
            return response($resultado->message, 422);
        
        $xml_timbrado = $resultado->xml;
        return $xml_timbrado;     
    }
}
```

### Consultas

```php
use Ralego\Timbox\Traits\Status;

class HomeController extends Controller
{
    use Status,

    // ...
}
```
Ejemplo para obtener el estatus de un cfdi: 

```php
use Illuminate\Http\Request;
use Ralego\Timbox\Traits\Status;

class HomeController extends Controller
{
    use Status;

    public function estatus()
    {
        // Usuario y contraseña de la cuenta de timbox
        $credenciales = (object)[
            'username'  => 'AAA010101000',
            'password'  => 'h6584D56fVdBbSmmnB'
        ];
        // Colocamos el uuid, rfc_emisor, rfc_receptor y el total del cfdi
        $resultado = $this->status($credenciales, 'EE728194-B356-4B84-9349-254F532A3E01', 'AAA010101AAA','IAD121214B34', '1751.60');
        // Si ocurre algun error retornamos el mensaje con status 422
        if(!$resultado->success)
            return response($resultado->message, 422);
        
        $estatus = $resultado->estado;
        return $estatus;     
    }
}
```

Ejemplo para consultar peticiones pendientes:

```php
use Illuminate\Http\Request;
use Ralego\Timbox\Traits\Status;

class HomeController extends Controller
{
    use Status;

    public function pendientes()
    {
        // Usuario y contraseña de la cuenta de timbox
        $credenciales = (object)[
            'username'  => 'AAA010101000',
            'password'  => 'h6584D56fVdBbSmmnB'
        ];
         // Cargamos el certificado y la llave del SAT en formato PEM
        $cer = file_get_contents('CSD01_AAA010101AAA.cer.pem');
        $key = file_get_contents('CSD01_AAA010101AAA.key.pem');
        // Colocamos al final el rfc_receptor
        $result = $this->pendientes($credenciales, $cer, $key, 'AAA010101AAA');
        // Si ocurre algun error retornamos el mensaje con status 422
        if(!$resultado->success)
            return response($resultado->message, 422);
        
        $uuids_pendientes = $resultado->uuids;
        return $uuids_pendientes;     
    }
}
```

Ejemplo para consultar los documentos relacionados de un cfdi:

```php
use Illuminate\Http\Request;
use Ralego\Timbox\Traits\Status;

class HomeController extends Controller
{
    use Status;

    public function relacionados()
    {
        // Usuario y contraseña de la cuenta de timbox
        $credenciales = (object)[
            'username'  => 'AAA010101000',
            'password'  => 'h6584D56fVdBbSmmnB'
        ];
         // Cargamos el certificado y la llave del SAT en formato PEM
        $cer = file_get_contents('CSD01_AAA010101AAA.cer.pem');
        $key = file_get_contents('CSD01_AAA010101AAA.key.pem');
        // Colocamos al final el rfc_receptor y el uuid
        $result = $this->relacionados($credenciales, $cer, $key, 'AAA010101AAA','EE728194-B356-4B84-9349-254F532A3E01');
        // Si ocurre algun error retornamos el mensaje con status 422
        if(!$resultado->success)
            return response($resultado->message, 422);
        
        $uuids_relacionados = $resultado;
        return $uuids_relacionados;     
    }
}
```

Ejemplo para realizar la petición de aceptacion/rechazo de la solicitud de cancelación:

```php
use Illuminate\Http\Request;
use Ralego\Timbox\Traits\Status;

class HomeController extends Controller
{
    use Status;

    public function responder()
    {
        // Usuario y contraseña de la cuenta de timbox
        $credenciales = (object)[
            'username'  => 'AAA010101000',
            'password'  => 'h6584D56fVdBbSmmnB'
        ];
         // Cargamos el certificado y la llave del SAT en formato PEM
        $cer = file_get_contents('CSD01_AAA010101AAA.cer.pem');
        $key = file_get_contents('CSD01_AAA010101AAA.key.pem');
        // Colocamos el rfc del receptor
        $respuestas = [
            [
                "uuid" => 'EE728194-B356-4B84-9349-254F532A3E01',
                "rfc_emisor" => 'AAA010101AAA',
                "total" => '1751.60',
                "respuesta" => 'A'
            ] // ... n folios_respuestas a procesar
        ];
        $resultado = $this->responder($credenciales, $cer, $key, 'IAD121214B34', $respuestas);
        // Si ocurre algun error retornamos el mensaje con status 422
        if(!$resultado->success)
            return response($resultado->message, 422);
        
        $folios = $resultado->folios;
        return $folios;     
    }
}
```

## Soporte

Cualquier duda referente a la información retornada por el web service de timbox lo puedes consultar en su [documentación](https://www.timbox.com.mx/timbrado/).