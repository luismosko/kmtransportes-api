<?php 
// v1.1.0 - Campos numericos deixam de bloquear submit silenciosamente (badInput) + erros inline
// Default-deny: campos numericos aceitam SOMENTE digitos (e ponto/virgula nos decimais),
// sanitizados no cliente (mascara) e no servidor (Ssw::coletar). Qualquer outro caractere e descartado.
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
                <div id="erro-form" class="alert alert-danger d-none" role="alert"></div>
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
                    <input type="text" inputmode="numeric" name="numeroNF" value="<?= $_POST["numeroNF"] ?>" class="form-control num-int">
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
                    <input type="text" inputmode="numeric" required="required" value="<?= $_POST["quantidade"] ?>" name="quantidade" id="campoQuantidade" class="form-control num-int">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label"><b>Peso em Kg da carga:</b></label>
                    <input type="text" inputmode="decimal" required="required" value="<?= $_POST["peso"] ?>" name="peso" id="campoPeso" class="form-control num-dec">
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
                    <input type="text" inputmode="decimal" name="cubagem" value="<?= $_POST["cubagem"] ?>" id="campoCubagem" class="form-control num-dec">
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
        // MASCARA NUMERICA (allowlist de caracteres)
        // Evita que o navegador bloqueie o submit sem mensagem (badInput
        // em input[type=number] quando o usuario digita texto, ex: "nao")
        // ========================================
        function limparNumero(valor, aceitaDecimal) {
            var v = String(valor || '').replace(',', '.');
            v = aceitaDecimal ? v.replace(/[^0-9.]/g, '') : v.replace(/[^0-9]/g, '');
            if (aceitaDecimal) {
                var partes = v.split('.');
                if (partes.length > 2) v = partes.shift() + '.' + partes.join('');
            }
            return v;
        }

        $(document).on('input blur', '.num-int', function() {
            var limpo = limparNumero(this.value, false);
            if (this.value !== limpo) this.value = limpo;
        });
        $(document).on('input blur', '.num-dec', function() {
            var limpo = limparNumero(this.value, true);
            if (this.value !== limpo) this.value = limpo;
        });

        // ========================================
        // ERRO INLINE (substitui alert e da feedback visivel)
        // ========================================
        function mostrarErro(msg, $campo) {
            $('#erro-form').html(msg).removeClass('d-none');
            if ($campo && $campo.length) {
                $campo.focus();
                $('html, body').animate({ scrollTop: $('#erro-form').offset().top - 120 }, 200);
            } else {
                $('html, body').animate({ scrollTop: $('#erro-form').offset().top - 120 }, 200);
            }
        }
        function limparErro() {
            $('#erro-form').addClass('d-none').html('');
        }
        // Somente no evento 'input' (digitacao). Nao usar 'change': o datetimepicker
        // dispara change ao selecionar e apagaria a mensagem de erro recem-exibida.
        $(document).on('input', '#form-rastreio input', limparErro);
        
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
                mostrarErro('<b>Horário inválido.</b> A coleta precisa de no mínimo 1 hora de antecedência. Horário mínimo permitido: <b>' +
                    minimo.toLocaleString('pt-BR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}) + '</b>');

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
            
            limparErro();

            if (!valor) {
                e.preventDefault();
                mostrarErro('<b>Preencha a data e hora</b> para realizar a coleta.', $('#campoLimiteColeta'));
                return false;
            }
            
            const dataSelecionada = parsearDataBR(valor);
            const minimo = getHorarioMinimo();
            
            if (!dataSelecionada) {
                e.preventDefault();
                mostrarErro('<b>Formato de data/hora inválido.</b> Use o formato DD/MM/AAAA HH:MM.', $('#campoLimiteColeta'));
                return false;
            }
            
            if (dataSelecionada < minimo) {
                e.preventDefault();
                mostrarErro('<b>Horário inválido.</b> A coleta precisa de no mínimo 1 hora de antecedência. Horário mínimo permitido: <b>' +
                    minimo.toLocaleString('pt-BR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}) + '</b>');
                $('#campoLimiteColeta').val('');
                $('#campoLimiteColeta').focus();
                return false;
            }

            // Numericos: sanitiza e valida com mensagem visivel
            $('.num-int').each(function() { this.value = limparNumero(this.value, false); });
            $('.num-dec').each(function() { this.value = limparNumero(this.value, true); });

            var qtd = parseInt($('#campoQuantidade').val(), 10);
            if (!qtd || qtd <= 0) {
                e.preventDefault();
                mostrarErro('<b>Informe a quantidade de volumes</b> (apenas números, maior que zero).', $('#campoQuantidade'));
                return false;
            }

            var peso = parseFloat($('#campoPeso').val());
            if (!peso || peso <= 0) {
                e.preventDefault();
                mostrarErro('<b>Informe o peso em Kg</b> (apenas números, use ponto ou vírgula para decimais).', $('#campoPeso'));
                return false;
            }

            return true;
        });
    });
</script>
<?php include 'footer.inc.php'; ?>
