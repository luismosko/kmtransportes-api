<?php
// v1.1.0 - Correção parâmetros API SSW conforme documentação
// https://ssw.inf.br/ajuda/
// Backup: branch backup-v1.0.0

class Ssw
{
    public $domain = 'KMT';
    public $user = 'sitekm';
    public $password = '030117';
    public $url = 'https://ssw.inf.br/';

    public function cotar($data)
    {
        $data = [
            'dominio' => $this->domain,
            "login" => $this->user,
            "senha" => $this->password,
            "cnpjPagador" => str_replace("/", "", str_replace("-", "", str_replace(".", "", $data["cnpjPagador"]))),
            "cepOrigem" => str_replace("-", "", $data["cepOrigem"]),
            "cepDestino" => str_replace("-", "", $data["cepDestino"]),
            "valorNF" => $_POST["valorNF"],
            "quantidade" =>    $_POST["quantidade"],
            "peso" => $_POST["peso"],
            "volume" =>    $_POST["volume"],
            "mercadoria" =>    $_POST["mercadoria"],
            "cnpjDestinatario" => str_replace("/", "", str_replace("-", "", str_replace(".", "", $data["cnpjDestinatario"]))),
            "coletar" => $_POST["coletar"],
            "entDificil" => "",
            "destContribuinte" => ""
        ];
        return $this->soap("sswCotacao/index.php", "cotar", $data);
    }

    public function coletar($data)
    {
        $dateHourObj = DateTime::createFromFormat('d/m/Y H:i', $data["limiteColeta"]);
        $currentDateTime = new DateTime();

        $dateHour = explode(" ", $data["limiteColeta"]);
        $date = explode("/", $dateHour[0]);
        $data["limiteColeta"] = $date[2] . "-" . $date[1] . "-" . $date[0] . "T" . $dateHour[1] . ":00";

        $data = [
            'dominio' => $this->domain,
            "login" => $this->user,
            "senha" => $this->password,
            "cnpjRemetente" => str_replace("/", "", str_replace("-", "", str_replace(".", "", $data["cnpjRemetente"]))),
            "cnpjDestinatario" => str_replace("/", "", str_replace("-", "", str_replace(".", "", $data["cnpjDestinatario"]))),
            "numeroNF" => $data["numeroNF"],
            "tipoPagamento" => $data["tipoPagamento"],
            "enderecoEntrega" => $data["enderecoEntrega"],
            "cepEntrega" => str_replace("-", "", $data["cepEntrega"]),
            "solicitante" => $data["solicitante"],
            "limiteColeta" => $data["limiteColeta"],
            "quantidade" => $data["quantidade"],
            "peso" => $data["peso"],
            "observacao" => $data["observacao"] . "\n\n" . $data["obsColeta"],
            "instrucao" => $data["instrucao"],
            "cubagem" => $data["cubagem"],
            "valorMerc" => "",
            "especie" => "",
            "chaveNF" => "",
            "cnpjSolicitante" => "",
            "nroPedido" => ""

        ];
        $result = $this->soap("sswColeta/index.php", "coletar", $data);
        // Se $result for bool (false), converte para objeto de erro genérico
        if (!is_object($result)) {
            $result = (object)[
                'erro' => "1",
                'mensagem' => "Erro ao processar a solicitação de coleta."
            ];
        }
        if ($dateHourObj < $currentDateTime) {
            $result->erro = "1";
            $result->mensagem = "O limite para realizar a coleta precisa ser uma data futura.";
        }
        return $result;
    }

    // ========================================
    // MÉTODOS DE RASTREAMENTO - v1.1.0
    // Corrigido conforme documentação SSW
    // ========================================
    
    public function trackingpf($data)
    {
        // trackingpf (pessoa física): dominio + usuario + senha + cpf + filtro
        $data['dominio'] = $this->domain;
        $data['usuario'] = $this->user;
        $data['senha'] = $this->password;
        return $this->postRaw('api/trackingpf', $data);
    }

    public function trackingdest($data)
    {
        // trackingdest (destinatário CNPJ): cnpj + senha + filtro
        // NÃO usa dominio/usuario conforme documentação!
        $data['senha'] = $this->password;
        return $this->postRaw('api/trackingdest', $data);
    }

    public function tracking($data)
    {
        // tracking (remetente): dominio + usuario + cnpj + filtro
        $data['dominio'] = $this->domain;
        $data['usuario'] = $this->user;
        return $this->postRaw('api/tracking', $data);
    }

    public function soap($address, $method, $data)
    {
        try {
            $client = new SoapClient($this->url . "ws/" . $address . "?wsdl");
            $result = $client->__soapCall($method, $data);
            return simplexml_load_string($result);
        } catch (SoapFault $E) {
            var_dump($E->faultstring);
            die;
        }
    }

    public function post($method, $data)
    {
        $url = $this->url . $method;
        $curl = curl_init();

        $data['dominio'] = $this->domain;
        $data['usuario'] = $this->user;

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ]);
        $response = simplexml_load_string(curl_exec($curl));
        curl_close($curl);
        return $response;
    }
    
    // v1.1.0 - Método que não adiciona parâmetros automaticamente
    // Cada método de tracking define seus próprios parâmetros
    public function postRaw($method, $data)
    {
        $url = $this->url . $method;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        
        // Tenta parsear como XML, se falhar retorna objeto de erro
        $xml = @simplexml_load_string($response);
        if ($xml === false) {
            return (object)['success' => 'false', 'error' => 'Resposta inválida da API'];
        }
        return $xml;
    }
}
