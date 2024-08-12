<?php

if (!defined('SOAP_1_1')) {
    define('SOAP_1_1', 1);
}

if (!defined('SOAP_1_2')) {
    define('SOAP_1_2', 2);
}

require_once __DIR__.'/libs/Requests/Requests.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

#[\AllowDynamicProperties]
class Afip {
    var $sdk_version_number = '1.1.1';
    var $CERT;
    var $PRIVATEKEY;
    var $CUIT;
    var $implemented_ws = array(
        'ElectronicBilling',
        'RegisterScopeFour',
        'RegisterScopeFive',
        'RegisterInscriptionProof',
        'RegisterScopeTen',
        'RegisterScopeThirteen'
    );
    var $options;

    function __construct($options)
    {
        ini_set("soap.wsdl_cache_enabled", "0");

        if (!isset($options['CUIT'])) {
            throw new Exception("CUIT field is required in options array");
        } else {
            $this->CUIT = $options['CUIT'];
        }

        if (!isset($options['production'])) {
            $options['production'] = FALSE;
        }

        if (!isset($options['cert'])) {
            $options['cert'] = NULL;
        }

        if (!isset($options['key'])) {
            $options['key'] = NULL;
        }

        $this->options = $options;

        $this->CERT        = $options['cert'];
        $this->PRIVATEKEY  = $options['key'];
    }

    public function GetServiceTA($service, $force = FALSE)
    {
        $client = new Client();

        $data = array(
            'environment' => $this->options['production'] === TRUE ? "prod" : "dev",
            'wsid' => $service,
            'tax_id' => $this->options['CUIT'],
            'force_create' => $force
        );

        if (isset($this->CERT)) {
            $data['cert'] = $this->CERT;
        }

        if ($this->PRIVATEKEY) {
            $data['key'] = $this->PRIVATEKEY;
        }

        $headers = [
            'Content-Type' => 'application/json',
            'sdk-version-number' => $this->sdk_version_number,
            'sdk-library' => 'php',
            'sdk-environment' => $this->options['production'] === TRUE ? "prod" : "dev"
        ];

        if (isset($this->options['access_token'])) {
            $headers['Authorization'] = 'Bearer ' . $this->options['access_token'];
        }

        try {
            $response = $client->post('https://app.afipsdk.com/api/v1/afip/auth', [
                'headers' => $headers,
                'json' => $data
            ]);

            $decoded_res = json_decode($response->getBody());

            return new TokenAuthorization($decoded_res->token, $decoded_res->sign);
        } catch (RequestException $e) {
            $error_message = $e->getMessage();

            if ($e->hasResponse()) {
                try {
                    $json_res = json_decode($e->getResponse()->getBody());

                    if (isset($json_res->message)) {
                        $error_message = $json_res->message;
                    }
                } catch (Exception $e) {}
            }

            throw new Exception($error_message);
        }
    }

    public function GetLastRequestXML()
    {
        $client = new Client();

        $headers = [
            'sdk-version-number' => $this->sdk_version_number,
            'sdk-library' => 'php',
            'sdk-environment' => $this->options['production'] === TRUE ? "prod" : "dev"
        ];

        if (isset($this->options['access_token'])) {
            $headers['Authorization'] = 'Bearer ' . $this->options['access_token'];
        }

        try {
            $response = $client->get('https://app.afipsdk.com/api/v1/afip/requests/last-xml', [
                'headers' => $headers
            ]);

            $decoded_res = json_decode($response->getBody());

            return $decoded_res;
        } catch (RequestException $e) {
            $error_message = $e->getMessage();

            if ($e->hasResponse()) {
                try {
                    $json_res = json_decode($e->getResponse()->getBody());

                    if (isset($json_res->message)) {
                        $error_message = $json_res->message;
                    }
                } catch (Exception $e) {}
            }

            throw new Exception($error_message);
        }
    }

    public function WebService($service, $options = array())
    {
        $options['service'] = $service;
        $options['generic'] = TRUE;

        return new AfipWebService($this, $options);
    }

    public function CreateCert($username, $password, $alias)
    {
        $client = new Client();

        $data = array(
            'environment' => $this->options['production'] === TRUE ? "prod" : "dev",
            'tax_id' => $this->options['CUIT'],
            'username' => $username,
            'password' => $password,
            'alias' => $alias
        );

        $headers = [
            'Content-Type' => 'application/json',
            'sdk-version-number' => $this->sdk_version_number,
            'sdk-library' => 'php',
            'sdk-environment' => $this->options['production'] === TRUE ? "prod" : "dev"
        ];

        if (isset($this->options['access_token'])) {
            $headers['Authorization'] = 'Bearer ' . $this->options['access_token'];
        }

        $retry = 24;

        while ($retry-- >= 0) {
            try {
                $response = $client->post('https://app.afipsdk.com/api/v1/afip/certs', [
                    'headers' => $headers,
                    'json' => $data
                ]);

                $decoded_res = json_decode($response->getBody());

                if ($decoded_res->status === 'complete') {
                    return $decoded_res->data;
                }

                if (isset($decoded_res->long_job_id)) {
                    $data['long_job_id'] = $decoded_res->long_job_id;
                }

                sleep(5);
            } catch (RequestException $e) {
                $error_message = $e->getMessage();

                if ($e->hasResponse()) {
                    try {
                        $json_res = json_decode($e->getResponse()->getBody());

                        if (isset($json_res->message)) {
                            $error_message = $json_res->message;
                        }
                    } catch (Exception $e) {}

                    throw new Exception($error_message);
                }
            }
        }

        throw new Exception('Error: Waiting for too long');
    }

    public function CreateWSAuth($username, $password, $alias, $wsid)
    {
        $client = new Client();

        $data = array(
            'environment' => $this->options['production'] === TRUE ? "prod" : "dev",
            'tax_id' => $this->options['CUIT'],
            'username' => $username,
            'password' => $password,
            'wsid' => $wsid,
            'alias' => $alias
        );

        $headers = [
            'Content-Type' => 'application/json',
            'sdk-version-number' => $this->sdk_version_number,
            'sdk-library' => 'php',
            'sdk-environment' => $this->options['production'] === TRUE ? "prod" : "dev"
        ];

        if (isset($this->options['access_token'])) {
            $headers['Authorization'] = 'Bearer ' . $this->options['access_token'];
        }

        $retry = 24;

        while ($retry-- >= 0) {
            try {
                $response = $client->post('https://app.afipsdk.com/api/v1/afip/ws-auths', [
                    'headers' => $headers,
                    'json' => $data
                ]);

                $decoded_res = json_decode($response->getBody());

                if ($decoded_res->status === 'complete') {
                    return $decoded_res->data;
                }

                if (isset($decoded_res->long_job_id)) {
                    $data['long_job_id'] = $decoded_res->long_job_id;
                }

                sleep(5);
            } catch (RequestException $e) {
                $error_message = $e->getMessage();

                if ($e->hasResponse()) {
                    try {
                        $json_res = json_decode($e->getResponse()->getBody());

                        if (isset($json_res->message)) {
                            $error_message = $json_res->message;
                        }
                    } catch (Exception $e) {}

                    throw new Exception($error_message);
                }
            }
        }

        throw new Exception('Error: Waiting for too long');
    }

    public function __get($property)
    {
        if (in_array($property, $this->implemented_ws)) {
            if (isset($this->{$property})) {
                return $this->{$property};
            } else {
                $file = __DIR__.'/Class/'.$property.'.php';
                if (!file_exists($file)) 
                    throw new Exception("Failed to open ".$file."\n", 1);

                require_once $file;

                return ($this->{$property} = new $property($this));
            }
        } else {
            return $this->{$property};
        }
    }
}

class TokenAuthorization {
    var $token;
    var $sign;

    function __construct($token, $sign)
    {
        $this->token    = $token;
        $this->sign     = $sign;
    }
}

#[\AllowDynamicProperties]
class AfipWebService {
    var $soap_version;
    var $WSDL;
    var $URL;
    var $WSDL_TEST;
    var $URL_TEST;
    var $afip;
    var $options;

    function __construct($afip, $options = array())
    {
        $this->afip = $afip;
        $this->options = $options;

        if (isset($options['WSDL'])) {
            $this->WSDL = $options['WSDL'];
        }

        if (isset($options['URL'])) {
            $this->URL = $options['URL'];
        }

        if (isset($options['WSDL_TEST'])) {
            $this->WSDL_TEST = $options['WSDL_TEST'];
        }

        if (isset($options['URL_TEST'])) {
            $this->URL_TEST = $options['URL_TEST'];
        }

        if (isset($options['generic']) && $options['generic'] === TRUE) {
            if (!isset($options['service'])) {
                throw new Exception("service field is required in options");
            }

            if (!isset($options['soap_version'])) {
                $options['soap_version'] = SOAP_1_2;
            }

            $this->soap_version = $options['soap_version'];
        }
    }

    public function GetTokenAuthorization($force = FALSE)
    {
        return $this->afip->GetServiceTA($this->options['service'], $force);
    }

    public function ExecuteRequest($method, $params = array())
    {
        $client = new Client();

        $data = array(
            'method' => $method,
            'params' => $params,
            'environment' => $this->afip->options['production'] === TRUE ? "prod" : "dev",
            'wsid' => $this->options['service'],
            'url' => $this->afip->options['production'] === TRUE ? $this->URL : $this->URL_TEST,
            'wsdl' => $this->afip->options['production'] === TRUE ? $this->WSDL : $this->WSDL_TEST,
            'soap_v_1_2' => $this->soap_version === SOAP_1_2
        );

        $headers = [
            'Content-Type' => 'application/json',
            'sdk-version-number' => $this->afip->sdk_version_number,
            'sdk-library' => 'php',
            'sdk-environment' => $this->afip->options['production'] === TRUE ? "prod" : "dev"
        ];

        if (isset($this->afip->options['access_token'])) {
            $headers['Authorization'] = 'Bearer ' . $this->afip->options['access_token'];
        }

        try {
            $response = $client->post('https://app.afipsdk.com/api/v1/afip/requests', [
                'headers' => $headers,
                'json' => $data
            ]);

            $decoded_res = json_decode($response->getBody());

            return $decoded_res;
        } catch (RequestException $e) {
            $error_message = $e->getMessage();

            if ($e->hasResponse()) {
                try {
                    $json_res = json_decode($e->getResponse()->getBody());

                    if (isset($json_res->message)) {
                        $error_message = $json_res->message;
                    }
                } catch (Exception $e) {}

                throw new Exception($error_message);
            }
        }
    }
}
