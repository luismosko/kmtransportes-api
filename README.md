# KM Transportes - API

Sistema de solicitação de coleta da KM Transportes.

## 🚀 Deploy Automático

Este repositório está configurado com **GitHub Actions** para deploy automático via FTP.

Sempre que fizer `push` na branch `main`, o código é enviado automaticamente para o servidor.

## 📁 Estrutura

```
├── coleta.php          # Formulário de solicitação de coleta
├── rastreamento.php    # Sistema de rastreamento
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

## 📝 Changelog

### v1.0.1 - Correção validação de horário
- Corrigido bug que permitia agendar coleta com menos de 1 hora de antecedência
- Adicionado alert popup para erros (antes só mostrava mensagem amarela)
- Bloqueio de horários inválidos no seletor de data/hora

---
*Mantido por Mosko Marketing*
