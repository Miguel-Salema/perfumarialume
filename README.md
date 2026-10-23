# Lume — Sistema de Perfumaria

Sistema web escolar/demonstrativo para a marca Lume, feito com PHP, MySQL, HTML, CSS e JavaScript.

## Identidade visual baseada na apresentação
- Marca: **Lume**
- Slogan: **Sinta o brilho. Seja a luz.**
- Cores principais: azul `#2b5e8b`, cinza `#cad0d1`, azul muito claro `#e7fbff`, branco `#ffffff`.
- Fontes: Agrandir (referência visual aproximada por sans-serif geométrica) e Darker Grotesque (slogan aproximado por sans-serif leve). O site usa fontes do sistema para funcionar sem dependência externa.
- Produtos apresentados: **Galasias** (pó facial, 30 g, resistente à água e efeito matte), **Chérie** (blush) e **Vías** (perfume de lavanda, 150 ml; lavanda fresca, bergamota e limão siciliano).

## Funcionalidades
- Página inicial com identidade da Lume e produtos em destaque.
- Catálogo e busca por nome/categoria.
- Página de detalhes do produto.
- Cadastro e login de clientes, com senha armazenada usando `password_hash`.
- Carrinho em sessão e finalização demonstrativa do pedido.
- Histórico de pedidos do cliente.
- Área administrativa protegida para cadastrar, editar e desativar produtos, visualizar pedidos e atualizar status.
- Consultas preparadas, validações no servidor e token CSRF nos formulários principais.
- Layout responsivo para celular e computador.

## Requisitos
- XAMPP (Apache + PHP + MySQL/MariaDB) ou ambiente equivalente.
- PHP 8.0+ recomendado.
- MySQL/MariaDB.

## Instalação no XAMPP
1. Extraia a pasta `Lume_Perfumaria` dentro de `C:\xampp\htdocs\`.
2. Abra o phpMyAdmin em `http://localhost/phpmyadmin`.
3. Importe `database.sql` (cria o banco `lume_perfumaria` e dados iniciais).
4. Confira as configurações em `config/database.php`. O padrão é usuário `root` sem senha, comum em instalações locais do XAMPP.
5. Acesse `http://localhost/Lume_Perfumaria/`.

## Conta administrativa inicial
- E-mail: `admin@lume.local`
- Senha: `Lume@123`
- Altere a senha depois do primeiro acesso. A senha inicial é demonstrativa para ambiente local/escolar.

## Observações
- O checkout registra um pedido demonstrativo, mas **não cobra pagamentos** nem integra transportadoras. Isso evita simular uma integração financeira real sem credenciais.
- Para uso real, configure HTTPS, troque a senha administrativa, revise permissões e adicione integração de pagamento/logística.
- A imagem dos produtos é representada por embalagens ilustradas em CSS para manter o projeto autocontido. Você pode trocar essas ilustrações por fotos oficiais autorizadas.
