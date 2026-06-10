# KM Transportes - API

Sistema de solicitação de coleta e rastreamento da KM Transportes.

## 🚀 Deploy Automático

Este repositório está configurado com **GitHub Actions** para deploy automático via FTP.

Sempre que fizer `push` na branch `main`, o código é enviado automaticamente para o servidor.

## 📁 Estrutura

```
├── coleta.php          # Formulário de solicitação de coleta
├── rastreamento.php    # Formulário de rastreamento
├── rastrear.php        # Processamento do rastreamento
├── ssw.php             # Classe de integração com API SSW
├── header.inc.php      # Header do site
├── footer.inc.php      # Footer do site
├── .github/
│   └── workflows/
│       └── deploy.yml  # Configuração do deploy automático
└── README.md
```

## ⚙️ Configuração dos Secrets

No GitHub, vá em **Settings → Secrets → Actions** e adicione:

| Secret | Valor |
|--------|-------|
| `FTP_SERVER` | ftp.kmtransportes.com.br |
| `FTP_USERNAME` | apilikes@api.kmtransportes.com.br |
| `FTP_PASSWORD` | (senha do FTP) |
| `FTP_PATH` | / |

## 🔄 Backup e Rollback

Para reverter para versão anterior:
```bash
git checkout backup-v1.0.0
```

## 📝 Changelog

### v1.1.0 - 2026-06-10 - Correção Rastreamento SSW

**Problema:** Rastreamento funcionava para alguns clientes e não para outros.

**Causa raiz:** 
1. Campo `client_doc_type` estava vazio no HTML (não detectava se era CPF ou CNPJ)
2. Parâmetros da API SSW estavam incorretos para cada endpoint:
   - `trackingdest`: enviava `dominio+usuario` mas deveria enviar `cnpj+senha`
   - `trackingpf`: faltava a `senha`

**Correções:**
- `rastreamento.php`: Adicionado JavaScript para detectar CPF/CNPJ automaticamente
- `rastrear.php`: Adicionado fallback para detectar tipo de documento
- `ssw.php`: Corrigido parâmetros conforme documentação SSW:
  - `trackingdest`: `cnpj` + `senha` + filtro
  - `trackingpf`: `dominio` + `usuario` + `senha` + `cpf` + filtro
  - `tracking`: `dominio` + `usuario` + `cnpj` + filtro

**Backup:** Branch `backup-v1.0.0`

### v1.0.1 - 2026-04-13 - Correção Validação Coleta

- Corrigido bug que permitia agendar coleta com menos de 1 hora de antecedência
- Adicionado alert popup para erros (antes só mostrava mensagem amarela)
- Bloqueio de horários inválidos no seletor de data/hora

---
*Mantido por Mosko Marketing*
