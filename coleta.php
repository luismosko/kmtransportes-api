<?php 
// v1.0.1 - Correção validação horário mínimo 1h antecedência
include 'header.inc.php';

$erro = false;
if (isset($_POST["method"]) && $_POST["method"] == "coletar") {
    $ssw = new Ssw;
    $result = $ssw->coletar($_POST);
    $title = ($result->erro == "0") ? "Solicitação de coleta registrada com sucesso." : "Erro ao registrar solicitação de coleta";
    $erro = ($result->erro == "0") ? false : true;
}

$title = (!isset($title)) ? "Preencha o formulário para registrar seu pedido de coleta" : $title;
?>
<h3 class="mb-5"><?= $title ?></h3>

<div class="panel panel-default shadow-sm p-3 mb-5 bg-white rounded">
    <div class="panel-body">
        <?php if (!$_POST || $erro) { ?>
            <?php if ($erro) { ?>
                <div class="alert alert-warning" role="alert">
                    <b><?= $result->mensagem; ?></b>
                </div>
            <?php } ?>
            <form method="post" id="form-rastreio" action="coleta.php">
                <input type='hidden' name="method" value="coletar">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label"><b>CPF ou CNPJ do remetente:</b></label>
                    <input type="text" required="required" value="<?= $_POST["cnpjRemetente"] ?>" name="cnpjRemetente" class="form-control cpfcnpj">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">CPF ou CNPJ do destinatário:</label>
                    <input type="text" name="cnpjDestinatario" value="<?= $_POST["cnpjDestinatario"] ?>" class="form-control cpfcnpj">
                </div>

                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Número da NF a ser coletada:</label>
                    <input type="number" name="numeroNF" value="<?= $_POST["numeroNF"] ?>" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label"><b>Pagamento:</b></label>
                    <select required="required" name="tipoPagamento" class="form-control">
                        <option value="">Selecione</option>
                        <option <?= ($_POST["tipoPagamento"] == "O") ? "selected" : "" ?> value="O">Origem</option>
                        <option <?= ($_POST["tipoPagamento"] == "D") ? "selected" : "" ?> value="D">Destino</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label"><b>CEP da entrega:</b></label>
                    <input type="text" required="required" value="<?= $_POST["cepEntrega"] ?>" name="cepEntrega" class="form-control cep">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Endereço da entrega:</label>
                    <input type="text" name="enderecoEntrega" value="<?= $_POST["enderecoEntrega"] ?>" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label"><b>Nome do solicitante da coleta:</b></label>
                    <input type="text" required="required" value="<?= $_POST["solicitante"] ?>" name="solicitante" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label"><b>Data e hora para realizar a coleta:</b></label>
                    <input type="text" required="required" value="<?= $_POST["limiteColeta"] ?>" name="limiteColeta" id="campoLimiteColeta" class="form-control datetime">
                    <div class="alert alert-warning mt-2 py-2 xsmall mb-0" role="alert">
                        ⚠️ <strong>Atenção:</strong> Revise a data e hora de coleta. Prazo mínimo de 1 hora de antecedência.
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label class="form-label"><b>Observação sobre a coleta:</b></label>
                    <input type="text" name="obsColeta" class="form-control" value="<?= $_POST["obsColeta"] ?>" placeholder="Escreva aqui observações importantes sobre a coleta (ex: portaria, acesso, contato no local, etc.)" />
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label"><b>Quantidade de volumes a serem coletados:</b></label>
                    <input type="number" required="required" value="<?= $_POST["quantidade"] ?>" name="quantidade" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label"><b>Peso em Kg da carga:</b></label>
                    <input type="number" step="0.001" required="required" value="<?= $_POST["peso"] ?>" name="peso" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Observações para a coleta:</label>
                    <input type="text" name="observacao" value="<?= $_POST["observacao"] ?>" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Instruções para a entrega:</label>
                    <input type="text" name="instrucao" value="<?= $_POST["instrucao"] ?>" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Cubagem em m3:</label>
                    <input type="number" step="0.0001" name="cubagem" value="<?= $_POST["cubagem"] ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Solicitar</button>
            </form>
        <?php } else { ?>
            <div class="alert alert-success" role="alert">
                Sua coleta está gerada com o número: <b><?= $result->numeroColeta ?></b>
            </div>
            <a href="coleta.php" class="btn btn-primary">Nova coleta</a>
        <?php } ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#nav-coleta').addClass('active');
        
        // ========================================
        // VALIDAÇÃO: Mínimo 1 hora de antecedência
        // ========================================
        const MINIMO_ANTECEDENCIA_MINUTOS = 60;
        
        // Função para calcular horário mínimo permitido
        function getHorarioMinimo() {
            const agora = new Date();
            agora.setMinutes(agora.getMinutes() + MINIMO_ANTECEDENCIA_MINUTOS);
            return agora;
        }
        
        // Função para parsear data no formato DD/MM/YYYY HH:MM
        function parsearDataBR(dataStr) {
            if (!dataStr) return null;
            // Formato esperado: DD/MM/YYYY HH:MM
            const partes = dataStr.match(/(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})/);
            if (!partes) return null;
            const [, dia, mes, ano, hora, minuto] = partes;
            return new Date(ano, mes - 1, dia, hora, minuto);
        }
        
        // Configurar datetimepicker com horário mínimo
        function configurarDateTimePicker() {
            const minimo = getHorarioMinimo();
            
            // Se estiver usando jQuery datetimepicker
            if ($.fn.datetimepicker) {
                $('.datetime').datetimepicker('destroy');
                $('.datetime').datetimepicker({
                    format: 'd/m/Y H:i',
                    minDate: 0, // Não permite datas passadas
                    minTime: false,
                    step: 30, // Intervalos de 30 minutos
                    onSelectDate: function(ct, $input) {
                        validarHorarioSelecionado($input);
                    },
                    onSelectTime: function(ct, $input) {
                        validarHorarioSelecionado($input);
                    },
                    onClose: function(ct, $input) {
                        validarHorarioSelecionado($input);
                    }
                });
            }
        }
        
        // Validar horário selecionado
        function validarHorarioSelecionado($input) {
            const valor = $input ? $input.val() : $('#campoLimiteColeta').val();
            const dataSelecionada = parsearDataBR(valor);
            const minimo = getHorarioMinimo();
            
            if (dataSelecionada && dataSelecionada < minimo) {
                alert('⚠️ ATENÇÃO!\n\nO horário da coleta precisa ter no mínimo 1 hora de antecedência.\n\nHorário mínimo permitido: ' + 
                    minimo.toLocaleString('pt-BR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}) +
                    '\n\nPor favor, selecione um horário válido.');
                
                if ($input) {
                    $input.val('');
                } else {
                    $('#campoLimiteColeta').val('');
                }
                return false;
            }
            return true;
        }
        
        // Inicializar configuração
        configurarDateTimePicker();
        
        // Atualizar a cada minuto
        setInterval(configurarDateTimePicker, 60000);
        
        // Validação ao mudar o campo
        $('#campoLimiteColeta').on('change blur', function() {
            validarHorarioSelecionado($(this));
        });
        
        // Validação ao submeter formulário
        $('#form-rastreio').on('submit', function(e) {
            const valor = $('#campoLimiteColeta').val();
            
            if (!valor) {
                e.preventDefault();
                alert('⚠️ ERRO!\n\nPor favor, selecione a data e hora para realizar a coleta.');
                $('#campoLimiteColeta').focus();
                return false;
            }
            
            const dataSelecionada = parsearDataBR(valor);
            const minimo = getHorarioMinimo();
            
            if (!dataSelecionada) {
                e.preventDefault();
                alert('⚠️ ERRO!\n\nFormato de data/hora inválido.\n\nUse o formato: DD/MM/AAAA HH:MM');
                $('#campoLimiteColeta').focus();
                return false;
            }
            
            if (dataSelecionada < minimo) {
                e.preventDefault();
                alert('⚠️ ERRO!\n\nO horário da coleta precisa ter no mínimo 1 hora de antecedência.\n\nHorário mínimo permitido: ' + 
                    minimo.toLocaleString('pt-BR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}) +
                    '\n\nPor favor, selecione um horário válido.');
                $('#campoLimiteColeta').val('');
                $('#campoLimiteColeta').focus();
                return false;
            }
            
            return true;
        });
    });
</script>
<?php include 'footer.inc.php'; ?>
