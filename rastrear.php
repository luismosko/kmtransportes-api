<?php 
// v1.1.0 - Correção bug rastreamento - parâmetros da API SSW
// Backup: branch backup-v1.0.0
include 'header.inc.php';

$data = [];

// Limpa o documento (remove formatação)
$client_doc = str_replace(["/", "-", "."], "", $_POST["client_doc"]);

// Detecta tipo do documento se não veio preenchido
$client_doc_type = $_POST["client_doc_type"];
if (empty($client_doc_type)) {
    $client_doc_type = (strlen($client_doc) <= 11) ? 'cpf' : 'cnpj';
}

// Define o parâmetro correto para a API
$data[$client_doc_type] = $client_doc;

// sigla_emp só é usado para tracking (remetente), não para trackingdest
if ($_POST["client_type"] == 'tracking') {
    $data["sigla_emp"] = $_POST["sigla_emp"];
}

// Adiciona documento de rastreio se informado
if (!empty($_POST["track_doc_type"]) && !empty($_POST["track_doc"])) {
    $data[$_POST["track_doc_type"]] = $_POST["track_doc"];
}

// Define método correto
// trackingpf = destinatário pessoa física (CPF)
// trackingdest = destinatário pessoa jurídica (CNPJ)  
// tracking = remetente
$method = $_POST["client_type"];
if ($_POST["client_type"] == 'trackingdest' && $client_doc_type == "cpf") {
    $method = 'trackingpf';
}

$ssw = new Ssw;
$result = $ssw->$method($data);

// Verifica sucesso - suporta tanto "true" (string) quanto 1 (int)
$isSuccess = ($result->success === true || $result->success === "true" || $result->success == 1);

$title = $isSuccess ? "Confira abaixo o rastreamento de sua encomenda" : "Não conseguimos rastrear sua encomendada";

$doc_types = [
    "nro_nf" => "Número da Nota Fiscal",
    "pedido" => "Número do pedido",
    "chave_nfe" => "Chave da Nota Fiscal Eletrônica",
    "nro_coleta" => "Número da coleta",
];
?>

<h3 class="mb-5"><?= $title ?></h3>

<div class="panel panel-default shadow-sm p-3 mb-5 bg-white rounded">
    <div class="panel-body">
        <?php if ($isSuccess && isset($result->tracking)) { ?>
            
            <?php if (isset($result->header)) { ?>
            <div class="alert alert-info mb-4">
                <div class="row">
                    <div class="col-md-6"><b>Remetente:</b> <?= $result->header->remetente ?? '-' ?></div>
                    <div class="col-md-6"><b>Destinatário:</b> <?= $result->header->destinatario ?? '-' ?></div>
                </div>
            </div>
            <?php } ?>
            
            <ul class="list-group mb-4">
                <li class="list-group-item active">
                    <div class="row">
                        <div class="col-md-12"><b>Histórico de Rastreamento</b></div>
                    </div>
                </li>

                <?php foreach ($result->tracking as $ocorrencia) { ?>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <b><?= date('d/m/Y H:i', strtotime($ocorrencia->data_hora)) ?></b>
                                <br><small class="text-muted"><?= $ocorrencia->cidade ?? '' ?></small>
                            </div>
                            <div class="col-md-9">
                                <strong class="text-primary"><?= $ocorrencia->ocorrencia ?? '' ?></strong>
                                <br><?= $ocorrencia->descricao ?? '' ?>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
            
        <?php } elseif ($isSuccess && isset($result->documento)) { ?>
            <!-- Formato XML antigo -->
            <?php foreach ($result->documento as $documento) { ?>
                <ul class="list-group mb-4">
                    <li class="list-group-item active">
                        <div class="row">
                            <div class="col-md-6"><b>Número CTRC:</b> <?= $documento->ctrc ?></div>
                            <div class="col-md-6"><b>Nota Fiscal:</b> <?= $documento->nf ?></div>
                        </div>
                    </li>

                    <?php foreach ($documento->ocorrencia as $ocorrencia) { ?>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-3">
                                    <b><?= $ocorrencia->dataHora ?></b>
                                </div>
                                <div class="col-md-9">
                                    <?= $ocorrencia->descricao ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        <?php } else { ?>
            <div class="alert alert-warning" role="alert">
                Não encontramos encomendas com os dados fornecidos. Faça novamente sua pesquisa ou entre em contato.
            </div>
        <?php } ?>
        <a href="rastreamento.php" class="btn btn-primary">Nova consulta</a>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#nav-rastreamento').addClass('active');
    });
</script>
<?php include 'footer.inc.php'; ?>
