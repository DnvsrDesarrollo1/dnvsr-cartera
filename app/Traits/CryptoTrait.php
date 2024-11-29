<?php

namespace App\Traits;

trait CryptoTrait
{
    /**
     * Descifra un texto cifrado utilizando AES-256-ECB con una clave dada.
     *
     */

    function decryptAES($textoCifrado, $key = '12345678')
    {
        if (empty($textoCifrado)) {
            throw new \InvalidArgumentException("El texto cifrado no puede estar vacío");
        }

        if (empty($key)) {
            throw new \InvalidArgumentException("La clave no puede estar vacía");
        }

        $key = hash('sha256', $key, true);

        $cipher = "aes-256-ecb";
        $options = OPENSSL_RAW_DATA;

        $textoCifradoDecoded = base64_decode($textoCifrado);

        $textoPlano = openssl_decrypt($textoCifradoDecoded, $cipher, $key, $options);

        if ($textoPlano === false) {
            throw new \RuntimeException("Error al descifrar: " . openssl_error_string());
        }

        return $textoPlano;
    }

    /**
     * Cifra un texto plano utilizando AES-256-ECB con una clave dada.
     *
     *   try {
     *   $textoPlano = "Texto a cifrar";
     *   $clave = "12345678";
     *   $textoCifrado = $this->encryptAES($textoPlano, $clave);
     *   echo "Texto cifrado: " . $textoCifrado;
     *   } catch (\Exception $e) {
     *       echo "Error: " . $e->getMessage();
     *   }
     */

    function encryptAES($textoPlano, $key = '12345678')
    {
        if (empty($textoPlano)) {
            throw new \InvalidArgumentException("El texto plano no puede estar vacío");
        }

        if (empty($key)) {
            throw new \InvalidArgumentException("La clave no puede estar vacía");
        }

        $key = hash('sha256', $key, true);

        $cipher = "aes-256-ecb";
        $options = OPENSSL_RAW_DATA;

        $textoCifrado = openssl_encrypt($textoPlano, $cipher, $key, $options);

        if ($textoCifrado === false) {
            throw new \RuntimeException("Error al cifrar: " . openssl_error_string());
        }

        return base64_encode($textoCifrado);
    }
}
