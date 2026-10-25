# 📍 Ponto WiFi — Sistema de Ponto Eletrônico por Rede WiFi

Sistema web PHP para controle de ponto eletrônico de funcionários via WiFi — o registro só é permitido quando o dispositivo está na rede do estabelecimento.

![Language](https://img.shields.io/badge/Stack-PHP%20%7C%20MySQL-purple) ![License](https://img.shields.io/badge/Licen%C3%A7a-MIT-blue)

---

## 🎯 O que faz

- Registro de ponto (entrada, almoço, volta, saída) somente via WiFi local
- Cadastro instantâneo de funcionários
- Painel admin com relatórios e estatísticas
- Mapa de presença em tempo real
- Diagnóstico de conexão WiFi
- API para integração com outros sistemas

## 🔐 Como funciona a segurança WiFi

O sistema detecta o IP local do dispositivo e só aceita o registro quando está na faixa de IP da rede interna. Funciona sem instalar nada — é só acessar a URL pelo celular conectado ao WiFi da empresa.

## 📂 Estrutura

```
ponto-wifi-php/
├── index.php              # Página de registro de ponto
├── funcionarios.php       # Cadastro de funcionários
├── relatorios.php         # Relatórios de ponto
├── estatisticas.php       # Dashboard de estatísticas
├── configuracoes.php      # Configurações do sistema
├── api_presenca.php       # API de presença
├── api_ping.php           # API de verificação WiFi
├── assets/                # CSS e JS
├── config.php             # Configurações gerais
└── data/                  # Dados JSON (ignorado pelo git)
```

## ⚙️ Instalação

1. Copie para servidor PHP 7.4+ (XAMPP local funciona)
2. Edite `config.php` com o nome da sua empresa
3. Acesse `index.php` pelo navegador
4. Cadastre os funcionários em `funcionarios.php`

## 📄 Licença

MIT

---
Feito com ☕ no Brasil 🇧🇷
