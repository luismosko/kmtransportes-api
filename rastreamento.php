<?php 
// v1.1.0 - Correção bug rastreamento - campo client_doc_type vazio
// Backup: branch backup-v1.0.0
include 'header.inc.php'; 
?>
<form method="post" id="form-rastreio" action="rastrear.php">
    <input type="hidden" name="client_doc_type" id="client-doc-type" value="cnpj">
    <input type="hidden" name="sigla_emp" value="KMT">
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Pesquisar por</label>
        <select name="client_type" required="required" class="form-control" id="client_type_select">
            <option value="">Selecione uma opção</option>
            <option value="tracking">Remetente</option>
            <option value="trackingdest">Destinatário</option>
        </select>
    </div>    
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">CPF ou CNPJ</label>
        <input required="required" type="text" name="client_doc" class="form-control cpfcnpj" id="cpfcnpj" aria-describedby="">
    </div>
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Tipo de documento para rastreio</label>
        <select name="track_doc_type" class="form-control" required="required">
            <option value="">Selecione uma opção</option>
            <option value="nro_nf">Número da Nota Fiscal</option>
            <option value="pedido">Número do pedido</option>
            <option value="chave_nfe">Chave da Nota Fiscal Eletrônica</option>
            <option value="nro_coleta">Número da coleta</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Documento para rastreio</label>
        <input type="text" name="track_doc" class="form-control" required="required">
    </div>
    <button type="submit" class="btn btn-primary">Rastrear encomenda</button>
</form>
<script>
    $(document).ready(function() {
        $('#nav-rastreamento').addClass('active');
        
        // ========================================
        // v1.1.0 - CORREÇÃO: Detectar CPF ou CNPJ automaticamente
        // O campo client_doc_type estava vazio, causando erro na API
        // ========================================
        $('#cpfcnpj').on('blur change keyup', function() {
            // Remove formatação para contar dígitos
            var valor = $(this).val().replace(/[^\d]/g, '');
            var tipo = 'cnpj'; // default
            
            if (valor.length <= 11) {
                tipo = 'cpf';
            } else {
                tipo = 'cnpj';
            }
            
            $('#client-doc-type').val(tipo);
            console.log('Tipo documento detectado:', tipo, '- Dígitos:', valor.length);
        });
        
        // Validação antes de submeter
        $('#form-rastreio').on('submit', function(e) {
            var doc = $('#cpfcnpj').val().replace(/[^\d]/g, '');
            var tipo = $('#client-doc-type').val();
            
            // Valida tamanho do documento
            if (tipo === 'cpf' && doc.length !== 11) {
                alert('CPF deve ter 11 dígitos.');
                e.preventDefault();
                return false;
            }
            if (tipo === 'cnpj' && doc.length !== 14) {
                alert('CNPJ deve ter 14 dígitos.');
                e.preventDefault();
                return false;
            }
            
            console.log('Enviando rastreamento:', {
                tipo: tipo,
                doc: doc,
                client_type: $('#client_type_select').val()
            });
        });
    });
</script>
<?php include "footer.inc.php"; ?>
