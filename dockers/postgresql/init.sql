-- ===========================================
-- SCRIPT DE CONFIGURAÇÃO DO BANCO DE DADOS
-- Sistema de Análise Contratual Automatizada
-- ===========================================

-- Criar banco de dados (se não existir)
-- CREATE DATABASE ross;

-- Conectar ao banco ross
-- \c ross;

-- ===========================================
-- EXTENSÕES NECESSÁRIAS
-- ===========================================
-- Habilitar extensão pgvector para embeddings
CREATE EXTENSION IF NOT EXISTS vector;

-- Habilitar extensão pgcrypto para gen_random_uuid()
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- ===========================================
-- TABELA: users
-- Armazena informações dos usuários
-- ===========================================
CREATE TABLE IF NOT EXISTS users (
    id UUID DEFAULT gen_random_uuid() NOT NULL,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    active BOOLEAN DEFAULT true NULL,
    is_superuser BOOLEAN DEFAULT false NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL,
    CONSTRAINT users_email_key UNIQUE (email),
    CONSTRAINT users_pkey PRIMARY KEY (id)
);

-- Comentários da tabela users
COMMENT ON TABLE users IS 'Tabela para armazenar informações dos usuários do sistema';
COMMENT ON COLUMN users.id IS 'ID único do usuário (UUID)';
COMMENT ON COLUMN users.nome IS 'Nome completo do usuário';
COMMENT ON COLUMN users.email IS 'Email único do usuário';
COMMENT ON COLUMN users.password IS 'Senha criptografada do usuário';
COMMENT ON COLUMN users.active IS 'Status ativo/inativo do usuário';
COMMENT ON COLUMN users.is_superuser IS 'Indica se o usuário é superusuário';
COMMENT ON COLUMN users.created_at IS 'Data/hora de criação do registro';
COMMENT ON COLUMN users.updated_at IS 'Data/hora da última atualização';

-- Índices para a tabela users
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_active ON users(active);
CREATE INDEX IF NOT EXISTS idx_users_created_at ON users(created_at);

-- ===========================================
-- TABELA: analysis_sections
-- Define as seções padronizadas de análise
-- ===========================================
CREATE TABLE IF NOT EXISTS analysis_sections (
    id SERIAL NOT NULL,
    title VARCHAR(255) NOT NULL,
    display_order INT2 NOT NULL,
    CONSTRAINT analysis_sections_pkey PRIMARY KEY (id),
    CONSTRAINT analysis_sections_title_key UNIQUE (title),
    CONSTRAINT analysis_sections_display_order_key UNIQUE (display_order)
);

-- Comentários da tabela analysis_sections
COMMENT ON TABLE analysis_sections IS 'Define as seções padronizadas de uma análise de contrato para manter a consistência.';
COMMENT ON COLUMN analysis_sections.id IS 'Identificador único para cada seção (Chave Primária).';
COMMENT ON COLUMN analysis_sections.title IS 'Título da seção (ex: "Seção 1: Extração de Dados Essenciais").';
COMMENT ON COLUMN analysis_sections.display_order IS 'Ordem em que a seção deve ser exibida na interface.';

-- ===========================================
-- TABELA: contracts
-- Armazena informações dos contratos
-- ===========================================
CREATE TABLE IF NOT EXISTS contracts (
    id VARCHAR(255) NOT NULL,
    original_filename TEXT NOT NULL,
    storage_path TEXT NULL,
    analyzed_at TIMESTAMPTZ NULL,
    created_at TIMESTAMPTZ DEFAULT now() NOT NULL,
    status VARCHAR DEFAULT 'pending'::character varying NULL,
    raw_text TEXT NULL,
    user_id UUID NULL,
    text_embedding vector(1536), -- Embedding do texto para busca semântica (extensão específica)
    CONSTRAINT contracts_pkey PRIMARY KEY (id)
);

-- Comentários da tabela contracts
COMMENT ON TABLE contracts IS 'Armazena metadados dos documentos de contrato que foram analisados.';
COMMENT ON COLUMN contracts.id IS 'Identificador único para cada contrato, fornecido externamente (Chave Primária).';
COMMENT ON COLUMN contracts.original_filename IS 'Nome do arquivo original do contrato que foi submetido.';
COMMENT ON COLUMN contracts.storage_path IS 'Caminho de armazenamento do arquivo original do contrato.';
COMMENT ON COLUMN contracts.analyzed_at IS 'Data e hora em que a análise foi concluída.';
COMMENT ON COLUMN contracts.created_at IS 'Data e hora do registro do contrato no sistema.';
COMMENT ON COLUMN contracts.raw_text IS 'Texto extraído do PDF';
COMMENT ON COLUMN contracts.user_id IS 'ID do usuário proprietário do contrato (opcional)';
COMMENT ON COLUMN contracts.text_embedding IS 'Embedding vetorial do texto para busca semântica (1536 dimensões)';

-- ===========================================
-- TABELA: analysis_data_points
-- Armazena pontos de análise da IA
-- ===========================================
CREATE TABLE IF NOT EXISTS analysis_data_points (
    id VARCHAR(255) NOT NULL,
    contract_id VARCHAR(255) NOT NULL,
    section_id INT4 NOT NULL,
    display_order INT2 DEFAULT 0 NOT NULL,
    "label" TEXT NULL,
    "content" TEXT NULL,
    details JSONB NULL,
    created_at TIMESTAMPTZ DEFAULT now() NOT NULL,
    CONSTRAINT analysis_data_points_pkey PRIMARY KEY (id),
    CONSTRAINT analysis_data_points_contract_id_fkey FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE,
    CONSTRAINT analysis_data_points_section_id_fkey FOREIGN KEY (section_id) REFERENCES analysis_sections(id)
);

-- Comentários da tabela analysis_data_points
COMMENT ON TABLE analysis_data_points IS 'Armazena cada item/ponto de dado individual da análise de um contrato.';
COMMENT ON COLUMN analysis_data_points.id IS 'Identificador único para cada ponto de dado da análise, fornecido externamente (Chave Primária).';
COMMENT ON COLUMN analysis_data_points.contract_id IS 'Referência ao contrato que está sendo analisado (Chave Estrangeira).';
COMMENT ON COLUMN analysis_data_points.section_id IS 'Referência à seção da análise à qual este ponto pertence (Chave Estrangeira).';
COMMENT ON COLUMN analysis_data_points.display_order IS 'Ordem de exibição do item dentro de sua seção.';
COMMENT ON COLUMN analysis_data_points."label" IS 'O rótulo, título ou chave do ponto de dado (ex: "Prazo de Vigência" ou "Risco 1").';
COMMENT ON COLUMN analysis_data_points."content" IS 'O conteúdo principal, descrição ou valor do ponto de dado.';
COMMENT ON COLUMN analysis_data_points.details IS 'Campo JSONB flexível para armazenar dados extras e estruturados, como recomendações, impactos ou listas.';
COMMENT ON COLUMN analysis_data_points.created_at IS 'Data/hora de criação do registro';

-- ===========================================
-- ÍNDICES PARA PERFORMANCE
-- ===========================================

-- Índice para consultas por contrato
CREATE INDEX IF NOT EXISTS idx_analysis_data_points_contract_id 
ON analysis_data_points(contract_id);

-- Índice para consultas por seção
CREATE INDEX IF NOT EXISTS idx_analysis_data_points_section_id 
ON analysis_data_points(section_id);

-- Índice para consultas por status
CREATE INDEX IF NOT EXISTS idx_contracts_status 
ON contracts(status);

-- Índice para consultas por data de criação
CREATE INDEX IF NOT EXISTS idx_contracts_created_at 
ON contracts(created_at);

-- Índice para busca semântica com pgvector
CREATE INDEX IF NOT EXISTS idx_contracts_text_embedding 
ON contracts USING ivfflat (text_embedding vector_cosine_ops) 
WITH (lists = 100);

-- Índice para consultas por data de análise
CREATE INDEX IF NOT EXISTS idx_contracts_analyzed_at 
ON contracts(analyzed_at);

-- Índice para consultas por usuário
CREATE INDEX IF NOT EXISTS idx_contracts_user_id 
ON contracts(user_id);

-- ===========================================
-- VIEWS ÚTEIS PARA CONSULTAS
-- ===========================================

-- View: Resumo de contratos
CREATE OR REPLACE VIEW v_contracts_summary AS
SELECT 
    c.id,
    c.original_filename,
    c.status,
    c.created_at,
    c.analyzed_at,
    COUNT(adp.id) as total_analysis_points,
    COUNT(CASE WHEN adp.section_id = 1 THEN 1 END) as dados_essenciais,
    COUNT(CASE WHEN adp.section_id = 2 THEN 1 END) as riscos_clausulas,
    COUNT(CASE WHEN adp.section_id = 3 THEN 1 END) as brechas_inconsistencias,
    COUNT(CASE WHEN adp.section_id = 4 THEN 1 END) as parecer_final
FROM contracts c
LEFT JOIN analysis_data_points adp ON c.id = adp.contract_id
GROUP BY c.id, c.original_filename, c.status, c.created_at, c.analyzed_at
ORDER BY c.created_at DESC;

-- View: Análise completa por contrato
CREATE OR REPLACE VIEW v_contract_analysis AS
SELECT 
    c.id as contract_id,
    c.original_filename,
    c.status,
    c.created_at,
    adp.section_id,
    CASE 
        WHEN adp.section_id = 1 THEN 'Dados Essenciais'
        WHEN adp.section_id = 2 THEN 'Riscos e Cláusulas'
        WHEN adp.section_id = 3 THEN 'Brechas e Inconsistências'
        WHEN adp.section_id = 4 THEN 'Parecer Final'
        ELSE 'Desconhecido'
    END as section_name,
    adp.display_order,
    adp.label,
    adp.content,
    adp.details
FROM contracts c
LEFT JOIN analysis_data_points adp ON c.id = adp.contract_id
ORDER BY c.created_at DESC, adp.section_id, adp.display_order;

-- ===========================================
-- DADOS DE EXEMPLO (OPCIONAL)
-- ===========================================

-- Inserir seções padronizadas (obrigatório para o sistema funcionar)
INSERT INTO analysis_sections (id, title, display_order) VALUES
    (1, 'Dados Essenciais', 1),
    (2, 'Riscos e Cláusulas', 2),
    (3, 'Brechas e Inconsistências', 3),
    (4, 'Parecer Final', 4)
ON CONFLICT (id) DO NOTHING;

-- Inserir contrato de exemplo (descomente se necessário)
/*
INSERT INTO contracts (id, original_filename, status, created_at) 
VALUES (
    'exemplo-12345-67890-abcdef',
    'contrato_exemplo.pdf',
    'processed',
    CURRENT_TIMESTAMP
);

INSERT INTO analysis_data_points (id, contract_id, section_id, display_order, label, content, details)
VALUES 
    (
        'ponto-12345-67890-abcdef',
        'exemplo-12345-67890-abcdef',
        1,
        1,
        'Partes Envolvidas',
        'CONTRATANTE: Empresa XYZ LTDA e CONTRATADA: Prestadora ABC LTDA',
        '{}'
    ),
    (
        'ponto-12345-67890-abcde2',
        'exemplo-12345-67890-abcdef',
        2,
        1,
        'Cláusula de Risco',
        'Identificada cláusula que pode gerar desequilíbrio contratual...',
        '{"Citação": "Trecho relevante do contrato", "Risco": "Descrição do risco identificado"}'
    );
*/

-- ===========================================
-- CONSULTAS ÚTEIS
-- ===========================================

-- Consulta 1: Contratos pendentes de análise
-- SELECT * FROM contracts WHERE status = 'pending';

-- Consulta 2: Contratos processados hoje
-- SELECT * FROM contracts WHERE DATE(analyzed_at) = CURRENT_DATE;

-- Consulta 3: Resumo de todos os contratos
-- SELECT * FROM v_contracts_summary;

-- Consulta 4: Análise completa de um contrato específico
-- SELECT * FROM v_contract_analysis WHERE contract_id = 'ID_DO_CONTRATO';

-- Consulta 5: Estatísticas gerais
-- SELECT 
--     COUNT(*) as total_contratos,
--     COUNT(CASE WHEN status = 'processed' THEN 1 END) as processados,
--     COUNT(CASE WHEN status = 'pending' THEN 1 END) as pendentes,
--     COUNT(CASE WHEN status = 'error' THEN 1 END) as com_erro
-- FROM contracts;

-- Consulta 6: Busca semântica (exemplo - requer embedding)
-- SELECT 
--     id, 
--     original_filename, 
--     1 - (text_embedding <=> '[0.1,0.2,0.3,...]'::vector) as similarity
-- FROM contracts 
-- WHERE text_embedding IS NOT NULL
-- ORDER BY text_embedding <=> '[0.1,0.2,0.3,...]'::vector
-- LIMIT 10;

-- ===========================================
-- FIM DO SCRIPT
-- ===========================================

-- Mensagem de sucesso
DO $$
BEGIN
    RAISE NOTICE 'Script de configuração executado com sucesso!';
    RAISE NOTICE 'Extensão pgvector habilitada para busca semântica';
    RAISE NOTICE 'Tabelas criadas: contracts, analysis_data_points';
    RAISE NOTICE 'Views criadas: v_contracts_summary, v_contract_analysis';
    RAISE NOTICE 'Índices criados para otimização de performance';
    RAISE NOTICE 'Índice de busca semântica configurado (ivfflat)';
    RAISE NOTICE 'Banco de dados pronto para uso!';
END $$;
