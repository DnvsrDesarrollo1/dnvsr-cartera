<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Beneficiary;
use App\Models\Plan;
use App\Traits\CryptoTrait;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QueryForm extends Component
{
    use CryptoTrait;

    private $x509 = 'MIIEOzCCAyOgAwIBAgIUY24rG+AD2e0B4sQ8ho3kvGCUXMowDQYJKoZIhvcNAQELBQAwgawxCzAJBgNVBAYTAkJPMQ8wDQYDVQQIDAZMQS1QQVoxDzANBgNVBAcMBkxBLVBBWjEkMCIGA1UECgwbQUdFTkNJQSBFU1RBVEFMIERFIFZJVklFTkRBMTcwNQYDVQQLDC5ESVJFQ0NJT04gTkFDSU9OQUwgREUgVklWSUVOREEgU09DSUFMIFJFU0lEVUFMMRwwGgYDVQQDDBNHRVNUSU9OIERFIENSRURJVE9TMB4XDTI0MTEyNTE0NTczOVoXDTI1MTEyNTE0NTczOVowgawxCzAJBgNVBAYTAkJPMQ8wDQYDVQQIDAZMQS1QQVoxDzANBgNVBAcMBkxBLVBBWjEkMCIGA1UECgwbQUdFTkNJQSBFU1RBVEFMIERFIFZJVklFTkRBMTcwNQYDVQQLDC5ESVJFQ0NJT04gTkFDSU9OQUwgREUgVklWSUVOREEgU09DSUFMIFJFU0lEVUFMMRwwGgYDVQQDDBNHRVNUSU9OIERFIENSRURJVE9TMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlesOHu1GRAAVtRLEmmJeBMvAiAc82DcFs+NNNNmp7oUQktDasvCmqrGA6tB0yhD4BY1lHsipRBpSGuWpGHjuJUUu/bH85LpAV6f2W5YNSKh+TtC/JU5606jWHgjvQmYwl9vsH1ZtPSZ6Jfd0uNtK1EGii2kU+Kky20DscOCPpmMdWmy7dFGfHYGgBGVzHU1Xzmj4EV9q+dl/cfLDkm9YlkcFIFvIEKrOo/+kKwUava0a4xJNfxnpuwEO3TPkU8UgGfJ2bOkWPg/UIg7mr05Nhvm5Ay4c7qBgqcc8nc+dus/aCq+PSh6NmdBBEkSzbRARwIpTbhl3JIZTCcuRYpaO+wIDAQABo1MwUTAdBgNVHQ4EFgQU+0EJKToBDchkw/Ii2BTG4E8p+fYwHwYDVR0jBBgwFoAU+0EJKToBDchkw/Ii2BTG4E8p+fYwDwYDVR0TAQH/BAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAQEAbefQLxIl558r4swpl+oc88KpAFhdMysknqkaFIHKjCzPtoLZm7WFwa+8y6EARDo0BKzzU86ewNz1bw9PnLH++IDYo4odiFDal8kZBhXSh6AzoYwEmEUFKmkM4VWZ1hnfC47eyxJZYZdgFCuzSQWidO9R3lNCe9+250SRTCAF82l7t/W/A3XWg5Kyrivp4My7x8uU68vZOUAPA2tBxyEPEfFYk2CT4WvU5ZjYU65KkTBXS/LxsEoYlwGJKd4Zxbx8Z7/7B26Zwgf2thsXgsfnKl2Hf7ABT3WkQvnNdMkZjnSx0BQFTusp1QRgcS5PLuMXrfZrzLsMjZrPzJlln1rLLw==';

    private $account = '10000020460402';
    public $idepro = '';
    public $ci = '';
    public $beneficiary = null;
    public $error = '';
    public $plan;

    public $qr = '';
    public $qrImage = '';

    private $servicio = [
        'GET' => 'CODIGOENTIDAD_QR_001',
        'CHECK' => 'CODIGOENTIDAD_QR_002',
        'DELETE' => 'CODIGOENTIDAD_QR_003',
        'RECOVER' => 'CODIGOENTIDAD_QR_004'
    ];

    private $resultCodes = [
        '0000' => 'Operación exitosa',
        '0001' => 'Firma digital inválida',
        '0002' => 'Error controlado',
        '0003' => 'Error por excepción',
        '0004' => 'Error por excepción en Back',
        '0005' => 'Usuario sin acceso a la operación solicitada',
        '0006' => 'Parámetros no establecidos para el usuario',
        '0007' => 'El importe especificado supera el límite máximo por transacción QR',
        '0008' => 'Número o estado de la cuenta inválido'
    ];

    public function updatedIdepro()
    {
        $this->buscarBeneficiario();
    }

    public function updatedCi()
    {
        $this->buscarBeneficiario();
    }

    private function buscarBeneficiario()
    {
        $this->qr = '';
        $this->qrImage = null;
        $this->beneficiary = null;
        $this->error = '';

        if (empty($this->idepro) && empty($this->ci)) {
            return;
        }

        try {
            $this->beneficiary = Beneficiary::where('idepro', $this->idepro)
                ->where('ci', $this->ci)
                ->first();

            $plans = Plan::where('idepro', $this->idepro)->orderBy('fecha_ppg', 'desc')->get();
            $acum = 0;
            $cuotas = 0;
            $nPlans = [];

            $capPagado = $this->beneficiary->saldo_credito;

            foreach ($plans as $p) {
                if ($acum < ($this->beneficiary->total_activado - $capPagado)) {
                    $nPlans[] = $p;
                    $acum += $p->prppgcapi;
                    $cuotas++;
                } else {
                    break;
                }
            }

            $this->plan = $nPlans[$cuotas - 1];

            if (!$this->beneficiary) {
                $this->error = 'No se encontró ningún beneficiario con los datos proporcionados.';
            }
        } catch (\Exception $e) {
            $this->error = 'Ocurrió un error al buscar el beneficiario. Por favor, inténtelo de nuevo.';
        }
    }

    public function generateQR()
    {
        $xml = new \SimpleXMLElement('<Envelope xmlns:ds="http://www.w3.org/2000/09/xmldsig#"></Envelope>');

        $this->addSignature($xml);
        $this->addObject($xml);

        $data = $xml->asXML();

        $this->qr = base64_encode($data);

        $this->generateQRImage($data);

        \App\Models\Image::create([
            'request_status' => 'PENDING',
            'image_b64' => $this->qr,
            'image_json' =>  $this->xmlToJson($data),
            'ci' => $this->beneficiary->ci,
            'idepro' => $this->beneficiary->idepro,
            'image_xml' => $data
        ]);

        $this->js("
                    Swal.fire({
                        html: `
                            <div class=\"p-4\">
                                <h3 class=\"text-xl font-semibold mb-3 text-center text-green-700\">
                                    QR generado exitósamente!
                                </h3>
                                <div class=\"flex flex-col justify-center w-fit mx-auto mb-3\">
                                    <img src=\"" . $this->qrImage . "\" alt=\"QR Code\" id=\"qrImage\">
                                    <p>
                                        Cobro por: Bs. <b>" . number_format($this->plan->prppgtota, 2) . "</b>
                                    </p>
                                </div>
                                <hr/>
                                <p class=\"mt-3 text-sm text-gray-600 text-center\">
                                    Escanee o descargue este código QR para realizar el pago.
                                </p>
                                <p class=\"mt-3 text -sm text-gray-600 text-center\">
                                    (Recuerde que puede renovar su QR pasados 30 segundos.)
                                </p>
                            </div>
                        `
                    });
                ");
    }

    private function addSignature(\SimpleXMLElement $xml)
    {
        $signature = $xml->addChild('ds:Signature');
        $signedInfo = $signature->addChild('ds:SignedInfo');
        $signedInfo->addChild('ds:CanonicalizationMethod')->addAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $signedInfo->addChild('ds:SignatureMethod')->addAttribute('Algorithm', 'rsa-sha1');

        $reference = $signedInfo->addChild('ds:Reference');
        $transforms = $reference->addChild('ds:Transforms');
        $transforms->addChild('ds:Transform')->addAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $reference->addChild('ds:DigestMethod')->addAttribute('Algorithm', 'sha1');
        $digestValue = $this->generateDigestValue();
        $reference->addChild('ds:DigestValue', $digestValue);

        $signatureValue = $this->generateSignatureValue();
        $signature->addChild('ds:SignatureValue', $signatureValue);

        $keyInfo = $signature->addChild('ds:KeyInfo');
        $x509Data = $keyInfo->addChild('ds:X509Data');
        $x509Certificate = hash('sha256', $this->x509);
        $x509Data->addChild('ds:X509Certificate', $x509Certificate);
        $keyInfo->addChild('ds:KeyName', 'EMPRESAQR1');
    }

    private function generateDigestValue()
    {
        $data = $this->beneficiary->idepro .
            $this->beneficiary->ci .
            $this->plan->prppgtota .
            $this->plan->prppgnpag .
            now()->timestamp;

        return base64_encode(sha1($data, true));
    }

    private function generateSignatureValue()
    {
        $data = $this->beneficiary->idepro .
            $this->beneficiary->ci .
            $this->plan->prppgtota .
            $this->plan->prppgnpag .
            now()->timestamp;

        return hash('sha256', $data);
    }

    private function addObject(\SimpleXMLElement $xml)
    {
        $object = $xml->addChild('Object');
        $object->addAttribute('Id', date('d/m/Y H:i:s'));

        $request = $object->addChild('Request');

        $this->addRequestHeader($request);
        $this->addRequestData($request);
    }

    private function addRequestHeader(\SimpleXMLElement $request)
    {
        $header = $request->addChild('RequestHeader');
        //$header->addChild('ApiKey', '01001');
        $header->addChild('Servicio', $this->servicio['GET']);

        $header->addChild('Usuario', 'usuarioPrueba');

        $encryptedPassword = $this->encryptAES('contraseña_api', 'secret_key');
        $header->addChild('Password', $encryptedPassword);

        $header->addChild('Fecha', date('d/m/Y'));
        $header->addChild('Hora', date('H:i:s'));
    }

    private function addRequestData(\SimpleXMLElement $request)
    {
        $data = $request->addChild('RequestData');
        $data->addChild('Cuenta', $this->account);
        $data->addChild('Importe', $this->plan->prppgtota);
        $data->addChild('Moneda', 'BOB');
        $data->addChild('Referencia', "PAGO_CUOTA|{$this->plan->idepro}|{$this->plan->prppgnpag}|" . csrf_token());
        $data->addChild('Validez', 'C');
        $data->addChild('FormatoQR', '1');

        $items = $data->addChild('Items');
        $item = $items->addChild('Item');
        $item->addChild('ItemDescripcion', 'TOTAL');
        $item->addChild('ItemValor', $this->plan->prppgtota);

        $items = $data->addChild('Items');
        $item = $items->addChild('Item');
        $item->addChild('ItemDescripcion', 'CAPITAL');
        $item->addChild('ItemValor', $this->plan->prppgcapi);

        $items = $data->addChild('Items');
        $item = $items->addChild('Item');
        $item->addChild('ItemDescripcion', 'INTERES');
        $item->addChild('ItemValor', $this->plan->prppginte);

        $items = $data->addChild('Items');
        $item = $items->addChild('Item');
        $item->addChild('ItemDescripcion', 'SEGURO_DESGRAVAMEN');
        $item->addChild('ItemValor', $this->plan->prppgsegu);

        $items = $data->addChild('Items');
        $item = $items->addChild('Item');
        $item->addChild('ItemDescripcion', 'OTROS');
        $item->addChild('ItemValor', $this->plan->prppgotro);

        $items = $data->addChild('Items');
        $item = $items->addChild('Item');
        $item->addChild('ItemDescripcion', 'FechaAtencion');
        $item->addChild('ItemValor', date('d/m/Y'));
    }

    private function generateQRImage($data)
    {
        $renderer = new ImageRenderer(
            new RendererStyle(500),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svgString = $writer->writeString($data);

        // Add a background color to the SVG
        $backgroundColor = '#0d1b2a'; // Set your desired background color
        $svgString = str_replace('#000000', $backgroundColor, $svgString);

        // Convert SVG to base64
        $base64 = base64_encode($svgString);
        $this->qrImage = 'data:image/svg+xml;base64,' . $base64;
    }

    private function xmlToJson($xml)
    {
        // Convierte el XML a un objeto SimpleXMLElement si es una cadena
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }

        // Convierte el objeto SimpleXMLElement a JSON y luego a un array asociativo
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);

        // Limpia el array resultante
        $array = $this->cleanXmlArray($array);

        // Convierte el array limpio de vuelta a JSON
        return json_encode($array);
    }

    private function cleanXmlArray($array)
    {
        foreach ($array as $key => $value) {
            // Elimina los atributos '@attributes' si están vacíos
            if ($key === '@attributes' && empty($value)) {
                unset($array[$key]);
                continue;
            }

            // Recursivamente limpia arrays anidados
            if (is_array($value)) {
                $array[$key] = $this->cleanXmlArray($value);
                // Si el array quedó vacío después de la limpieza, elimínalo
                if (empty($array[$key])) {
                    unset($array[$key]);
                }
            }

            // Convierte valores de un solo elemento en strings
            if (is_array($value) && count($value) === 1 && isset($value[0])) {
                $array[$key] = $value[0];
            }
        }

        return $array;
    }

    public function render()
    {
        return view('livewire.query-form');
    }
}
