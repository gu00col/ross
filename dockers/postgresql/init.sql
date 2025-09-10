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

-- ===========================================
-- TABELA: users
-- Armazena informações dos usuários
-- ===========================================
CREATE TABLE IF NOT EXISTS users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    active BOOLEAN DEFAULT true,
    is_superuser BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
-- TABELA: contracts
-- Armazena informações dos contratos
-- ===========================================
CREATE TABLE IF NOT EXISTS contracts (
    id VARCHAR(255) PRIMARY KEY,
    user_id UUID, -- ID do usuário (sem chave estrangeira por enquanto)
    original_filename VARCHAR(500) NOT NULL,
    storage_path TEXT,
    raw_text TEXT,
    text_embedding vector(1536), -- Embedding do texto para busca semântica
    status VARCHAR(50) DEFAULT 'pending',
    analyzed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Comentários da tabela contracts
COMMENT ON TABLE contracts IS 'Tabela para armazenar informações dos contratos processados';
COMMENT ON COLUMN contracts.id IS 'ID único do contrato (UUID)';
COMMENT ON COLUMN contracts.user_id IS 'ID do usuário proprietário do contrato (opcional)';
COMMENT ON COLUMN contracts.original_filename IS 'Nome original do arquivo PDF';
COMMENT ON COLUMN contracts.storage_path IS 'Caminho de armazenamento no Google Drive';
COMMENT ON COLUMN contracts.raw_text IS 'Texto extraído do PDF';
COMMENT ON COLUMN contracts.text_embedding IS 'Embedding vetorial do texto para busca semântica (1536 dimensões)';
COMMENT ON COLUMN contracts.status IS 'Status do processamento (pending, processed, error)';
COMMENT ON COLUMN contracts.analyzed_at IS 'Data/hora da análise pela IA';
COMMENT ON COLUMN contracts.created_at IS 'Data/hora de criação do registro';

-- ===========================================
-- TABELA: analysis_data_points
-- Armazena pontos de análise da IA
-- ===========================================
CREATE TABLE IF NOT EXISTS analysis_data_points (
    id VARCHAR(255) PRIMARY KEY,
    contract_id VARCHAR(255) NOT NULL,
    section_id INTEGER NOT NULL,
    display_order INTEGER,
    label VARCHAR(500),
    content TEXT,
    details JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE
);

-- Comentários da tabela analysis_data_points
COMMENT ON TABLE analysis_data_points IS 'Tabela para armazenar pontos de análise da IA';
COMMENT ON COLUMN analysis_data_points.id IS 'ID único do ponto de análise (UUID)';
COMMENT ON COLUMN analysis_data_points.contract_id IS 'ID do contrato relacionado';
COMMENT ON COLUMN analysis_data_points.section_id IS 'ID da seção (1=Dados, 2=Riscos, 3=Brechas, 4=Parecer)';
COMMENT ON COLUMN analysis_data_points.display_order IS 'Ordem de exibição dentro da seção';
COMMENT ON COLUMN analysis_data_points.label IS 'Título do ponto de análise';
COMMENT ON COLUMN analysis_data_points.content IS 'Conteúdo principal da análise';
COMMENT ON COLUMN analysis_data_points.details IS 'Detalhes adicionais em formato JSON';
COMMENT ON COLUMN analysis_data_points.created_at IS 'Data/hora de criação do registro';

-- ===========================================
-- ÍNDICES PARA PERFORMANCE
-- ===========================================

-- Índice para consultas por contrato
CREATE INDEX IF NOT EXISTS idx_analysis_contract_id 
ON analysis_data_points(contract_id);

-- Índice para consultas por seção
CREATE INDEX IF NOT EXISTS idx_analysis_section_id 
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
