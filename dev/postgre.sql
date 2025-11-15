-- PostgreSQL Database Dump
-- Converted from MySQL
-- Database: wwwhorigene_db

-- Set client encoding
SET client_encoding = 'UTF8';

-- --------------------------------------------------------

--
-- Structure de la table collec
--

CREATE TABLE collec (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL
);

--
-- Données de la table collec
--

INSERT INTO collec (id, name, description, comment) VALUES
(1, 'collection 1', 'description collection 1', 'commentaire collection 1'),
(2, 'collection 2', 'description collection 2', 'commentaire collection 2'),
(3, 'collection 3', 'description collection 3', 'commentaire collection 3'),
(4, 'collection 4', 'description collection 4', 'commentaire collection 4'),
(5, 'collection 5', 'description collection 5', 'commentaire collection 5');

-- Reset sequence
SELECT setval('collec_id_seq', (SELECT MAX(id) FROM collec));

-- --------------------------------------------------------

--
-- Structure de la table depot
--

CREATE TABLE depot (
  id SERIAL PRIMARY KEY,
  date_depot DATE NOT NULL,
  type VARCHAR(255) DEFAULT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL,
  "user" VARCHAR(255) NOT NULL,
  sample VARCHAR(255) NOT NULL
);

-- --------------------------------------------------------

--
-- Structure de la table doctrine_migration_versions
--

CREATE TABLE doctrine_migration_versions (
  version VARCHAR(191) PRIMARY KEY,
  executed_at TIMESTAMP DEFAULT NULL,
  execution_time INTEGER DEFAULT NULL
);

--
-- Données de la table doctrine_migration_versions
--

INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES
('DoctrineMigrations\Version20250221100045', '2025-02-21 11:01:00', 3133),
('DoctrineMigrations\Version20250221101445', '2025-02-21 11:14:51', 49),
('DoctrineMigrations\Version20250221103055', '2025-02-21 11:31:00', 43),
('DoctrineMigrations\Version20250221103752', '2025-02-21 11:38:02', 41),
('DoctrineMigrations\Version20250221105157', '2025-02-21 11:52:01', 52),
('DoctrineMigrations\Version20250221111640', '2025-02-21 12:16:45', 458),
('DoctrineMigrations\Version20250221114718', '2025-02-21 12:47:23', 5),
('DoctrineMigrations\Version20250221122142', '2025-02-21 13:21:47', 389),
('DoctrineMigrations\Version20250221122617', '2025-02-21 13:26:22', 1),
('DoctrineMigrations\Version20250221124509', '2025-02-21 13:45:15', 1),
('DoctrineMigrations\Version20250221125041', '2025-02-21 13:50:46', 1),
('DoctrineMigrations\Version20250221145316', '2025-02-21 15:53:24', 3),
('DoctrineMigrations\Version20250221145521', '2025-02-21 15:55:28', 3),
('DoctrineMigrations\Version20250221150222', '2025-02-21 16:02:37', 112),
('DoctrineMigrations\Version20250228135845', '2025-02-28 14:58:51', 1),
('DoctrineMigrations\Version20250228135927', '2025-02-28 14:59:33', 278),
('DoctrineMigrations\Version20250228141204', '2025-02-28 15:12:11', 34),
('DoctrineMigrations\Version20250228175719', '2025-02-28 18:57:24', 183),
('DoctrineMigrations\Version20250228175802', '2025-02-28 18:58:07', 1),
('DoctrineMigrations\Version20250322101756', '2025-03-22 11:18:02', 264),
('DoctrineMigrations\Version20250328060948', '2025-03-28 07:09:54', 128),
('DoctrineMigrations\Version20250328062205', '2025-03-28 07:22:09', 198),
('DoctrineMigrations\Version20250328064935', '2025-03-28 07:49:39', 78),
('DoctrineMigrations\Version20250328070505', '2025-03-28 08:05:09', 54),
('DoctrineMigrations\Version20250328074427', '2025-03-28 08:44:33', 3),
('DoctrineMigrations\Version20250328074556', '2025-03-28 08:46:02', 88),
('DoctrineMigrations\Version20250411144505', '2025-04-11 16:45:09', 37),
('DoctrineMigrations\Version20250411145649', '2025-04-11 16:56:53', 45),
('DoctrineMigrations\Version20250411150137', '2025-04-11 17:01:40', 79),
('DoctrineMigrations\Version20250414195911', '2025-04-14 21:59:16', 97),
('DoctrineMigrations\Version20250602101448', '2025-06-03 15:53:43', 134),
('DoctrineMigrations\Version20250602102435', '2025-06-03 16:01:36', 91),
('DoctrineMigrations\Version20250903154813', '2025-09-04 11:30:09', 413),
('DoctrineMigrations\Version20250903155602', '2025-09-04 11:30:09', 54),
('DoctrineMigrations\Version20250903163846', '2025-09-04 11:30:09', 204),
('DoctrineMigrations\Version20250904175130', '2025-09-04 20:40:35', 88),
('DoctrineMigrations\Version20250924155504', '2025-09-28 17:12:17', 205),
('DoctrineMigrations\Version20250926134001', '2025-09-28 17:12:17', 74),
('DoctrineMigrations\Version20250926141124', '2025-09-28 17:12:17', 88);

-- --------------------------------------------------------

--
-- Structure de la table drug_resistance
--

CREATE TABLE drug_resistance (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  type VARCHAR(255) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL
);

--
-- Données de la table drug_resistance
--

INSERT INTO drug_resistance (id, name, type, description, comment) VALUES
(23, 'Penicillines', 'quantitative', NULL, NULL),
(24, 'Cephalosporines', 'quantitative', 'ex. céfazoline', NULL),
(25, 'Macrolides', 'quantitative', 'ex. érythromycine', NULL),
(26, 'Fluoroquinolones', 'quantitative', 'ex. ciprofloxacine', NULL),
(27, 'Apramycine', 'quantitative', NULL, NULL),
(28, 'Tetracycline', 'quantitative', NULL, NULL),
(29, 'Imipeneme', 'quantitative', NULL, NULL),
(30, 'Rifampicine', 'quantitative', NULL, NULL);

SELECT setval('drug_resistance_id_seq', (SELECT MAX(id) FROM drug_resistance));

-- --------------------------------------------------------

--
-- Structure de la table genotype
--

CREATE TABLE genotype (
  id SERIAL PRIMARY KEY,
  type VARCHAR(255) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL
);

--
-- Données de la table genotype
--

INSERT INTO genotype (id, type, description, comment) VALUES
(1, 'WT', NULL, NULL),
(2, 'GMO', NULL, NULL);

SELECT setval('genotype_id_seq', (SELECT MAX(id) FROM genotype));

-- --------------------------------------------------------

--
-- Structure de la table phenotype_type
--

CREATE TABLE phenotype_type (
  id SERIAL PRIMARY KEY,
  type VARCHAR(255) NOT NULL
);

--
-- Données de la table phenotype_type
--

INSERT INTO phenotype_type (id, type) VALUES
(5, 'transformability'),
(6, 'conjuguaison'),
(7, 'CalciumSensible');

SELECT setval('phenotype_type_id_seq', (SELECT MAX(id) FROM phenotype_type));

-- --------------------------------------------------------

--
-- Structure de la table plasmyd
--

CREATE TABLE plasmyd (
  id SERIAL PRIMARY KEY,
  name_plasmyd VARCHAR(255) NOT NULL,
  type VARCHAR(255) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL,
  slug VARCHAR(255) DEFAULT NULL
);

--
-- Données de la table plasmyd
--

INSERT INTO plasmyd (id, name_plasmyd, type, description, comment, slug) VALUES
(25, 'pUC19', 'wt', 'Origine ColE1, ampicilline, MCS', NULL, 'pUC19 - wt'),
(27, 'pGEX-4T-1', 'wt', 'Ampicilline, GST tag', NULL, 'pGEX-4T-1 - wt'),
(28, 'p2AB5075', 'synthetic', 'tetA + the other natural plasmids', NULL, 'p2AB5075 - synthetic'),
(30, 'pBluescript SK(−)', 'wt', NULL, NULL, 'pBluescript SK(−) - wt'),
(31, 'pJET2.1', 'wt', 'Origine ColE1, ampicilline, MCS', NULL, 'pJET2.1 - wt'),
(57, 'pUC19-NG', 'WT', 'ajout du gene X', '', 'pUC19-NG - WT'),
(66, 'pGEX-NG-test', 'WT', '', '', 'pGEX-NG-test - WT'),
(67, 'pGEX-XC-test1', 'WT', '', '', 'pGEX-XC-test1 - WT'),
(68, 'plasmide_test', 'WT', '', '', 'plasmide_test - WT');

SELECT setval('plasmyd_id_seq', (SELECT MAX(id) FROM plasmyd));

-- --------------------------------------------------------

--
-- Structure de la table project
--

CREATE TABLE project (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL
);

--
-- Données de la table project
--

INSERT INTO project (id, name, description, comment) VALUES
(1, 'project 1', 'description projet 1', 'commentaire projet 1'),
(2, 'project 2', 'description projet 2', 'commentaire projet 2'),
(3, 'project 3', 'description projet 3', 'commentaire projet 3'),
(4, 'project 4', 'description projet 4', 'commentaire projet 4'),
(5, 'project 5', 'description projet 5', 'commentaire projet 5');

SELECT setval('project_id_seq', (SELECT MAX(id) FROM project));

-- --------------------------------------------------------

--
-- Structure de la table publication
--

CREATE TABLE publication (
  id SERIAL PRIMARY KEY,
  article_url VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  autor VARCHAR(255) NOT NULL,
  year VARCHAR(255) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  slug VARCHAR(255) DEFAULT NULL,
  doi VARCHAR(255) DEFAULT NULL
);

--
-- Données de la table publication
--

INSERT INTO publication (id, article_url, title, autor, year, description, slug, doi) VALUES
(2, 'http://test.com', 'titre A', 'auteur A', '2025', 'description publication A', 'titre A - auteur A - 2025', NULL),
(3, 'http://test.com', 'titre B', 'auteur B', '2025', 'description publication B', 'titre B - auteur B - 2025', NULL),
(4, 'http://test.com', 'titre C', 'auteur C', '2025', 'description publication C', 'titre C - auteur C - 2025', NULL);

SELECT setval('publication_id_seq', (SELECT MAX(id) FROM publication));

-- --------------------------------------------------------

--
-- Structure de la table sample
--

CREATE TABLE sample (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) DEFAULT NULL,
  type VARCHAR(255) DEFAULT NULL,
  date DATE NOT NULL,
  country VARCHAR(255) DEFAULT NULL,
  city VARCHAR(255) DEFAULT NULL,
  localisation VARCHAR(255) DEFAULT NULL,
  under_localisation VARCHAR(255) DEFAULT NULL,
  gps VARCHAR(255) DEFAULT NULL,
  environment VARCHAR(255) DEFAULT NULL,
  other VARCHAR(255) DEFAULT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL,
  "user" VARCHAR(255) NOT NULL
);

--
-- Données de la table sample
--

INSERT INTO sample (id, name, type, date, country, city, localisation, under_localisation, gps, environment, other, description, comment, "user") VALUES
(1, 'sample 1', NULL, '2025-02-26', 'FRANCE', 'Lyon', 'Université Lyon', NULL, NULL, NULL, NULL, NULL, NULL, '1'),
(2, 'sample 2', NULL, '2025-02-26', 'FRANCE', 'Lille', 'CHU Lille', NULL, NULL, NULL, NULL, NULL, NULL, '1'),
(3, 'sample 3', NULL, '2025-02-17', 'ITALIE', 'Rome', 'Colisée', 'test', NULL, NULL, NULL, NULL, NULL, '1');

SELECT setval('sample_id_seq', (SELECT MAX(id) FROM sample));

-- --------------------------------------------------------

--
-- Structure de la table "user"
--

CREATE TABLE "user" (
  id SERIAL PRIMARY KEY,
  email VARCHAR(180) NOT NULL UNIQUE,
  firstname VARCHAR(180) NOT NULL,
  lastname VARCHAR(180) NOT NULL,
  roles JSONB NOT NULL,
  password VARCHAR(255) NOT NULL,
  is_verified BOOLEAN NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

--
-- Données de la table "user"
--

INSERT INTO "user" (id, email, firstname, lastname, roles, password, is_verified, created_at) VALUES
(1, 'admin@test.fr', 'Baptiste', 'BERTRAND', '["ROLE_ADMIN"]', '$2y$13$KcrqG0c.6TXur02GyCfxdet4TKPvES/mMYmSJd7bbj3nF0bblD5LO', false, '2025-02-21 11:01:51'),
(2, 'intern@test.fr', 'nicolas', 'GAUDIN', '[]', '$2y$13$NSkc8NeSiCzahAnqq3b3fuc6oS6mnXBJCQ3ZlU1Oa95IvoQndQGHe', false, '2025-06-03 17:10:59'),
(3, 'search@test.fr', 'kelly', 'GOLDLUST', '["ROLE_SEARCH"]', '$2y$13$IhSEXtg4oVLcjSa57DQXEu./JVIRa.qw0YfB.lXIIYk1Pk0Itv0N.', false, '2025-07-23 23:00:18'),
(6, 'jerem@test.fr', 'Jeremy', 'Gony', '["ROLE_ADMIN"]', '$2y$13$INvr8a5HDeBifjWWzHi7gu.x5udhdh75CqcJ.1ZbT/qjJ8vv4kle2', false, '2025-11-07 06:26:05');

SELECT setval('user_id_seq', (SELECT MAX(id) FROM "user"));

-- --------------------------------------------------------

--
-- Structure de la table strain
--

CREATE TABLE strain (
  id SERIAL PRIMARY KEY,
  name_strain VARCHAR(255) NOT NULL,
  specie VARCHAR(100) NOT NULL,
  gender VARCHAR(100) NOT NULL,
  comment VARCHAR(255) DEFAULT NULL,
  description VARCHAR(255) DEFAULT NULL,
  created_by_name VARCHAR(255) DEFAULT NULL,
  date DATE DEFAULT NULL,
  parent_strain_id INTEGER DEFAULT NULL,
  genotype_id INTEGER DEFAULT NULL,
  depot_id INTEGER DEFAULT NULL,
  prelevement_id INTEGER DEFAULT NULL,
  created_by_id INTEGER NOT NULL,
  description_genotype VARCHAR(255) DEFAULT NULL,
  date_archive DATE DEFAULT NULL,
  info_genotype VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (parent_strain_id) REFERENCES strain(id),
  FOREIGN KEY (genotype_id) REFERENCES genotype(id),
  FOREIGN KEY (depot_id) REFERENCES depot(id),
  FOREIGN KEY (prelevement_id) REFERENCES sample(id),
  FOREIGN KEY (created_by_id) REFERENCES "user"(id)
);

--
-- Données de la table strain (exemples - liste tronquée pour la lisibilité)
--

INSERT INTO strain (id, name_strain, specie, gender, comment, description, created_by_name, date, parent_strain_id, genotype_id, depot_id, prelevement_id, created_by_id, description_genotype, date_archive, info_genotype) VALUES
(170, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL, NULL, NULL),
(187, 'AB5075', 'baumannii', 'Acinetobacter', 'Obtained from the collin''s lab.', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, 1, 1, NULL, NULL, NULL),
(188, 'AB5075', 'baumannii', 'Acinetobacter', NULL, NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 2, NULL, 2, 1, 'comM(FL) pho::ApraR #2', NULL, NULL),
(189, 'AB5075', 'baumannii', 'Acinetobacter', 'Insertion of the tetA gene on p2AB5075', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, 3, 1, NULL, NULL, NULL),
(190, 'DH5α', 'Escherichia coli', 'Escherichia', NULL, NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 2, NULL, 3, 1, 'Δ(araD-araB)567, ΔlacZ4787', NULL, NULL),
(191, 'M2', 'nosocomialis', 'Acinetobacter', 'Made by co-culturing of M2 rpoB (Box6C4) with 40288 ∆comEC::aac, annotated as AB25', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 2, NULL, 3, 1, 'rpoB* vgrG::Tn2006 #7', NULL, NULL);

-- Continuer avec les autres inserts...

SELECT setval('strain_id_seq', 342);

-- --------------------------------------------------------

--
-- Structure de la table drug_resistance_on_strain
--

CREATE TABLE drug_resistance_on_strain (
  id SERIAL PRIMARY KEY,
  concentration INTEGER NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL,
  drug_resistance_id INTEGER DEFAULT NULL,
  strain_id INTEGER DEFAULT NULL,
  resistant BOOLEAN DEFAULT NULL,
  name_file VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (drug_resistance_id) REFERENCES drug_resistance(id),
  FOREIGN KEY (strain_id) REFERENCES strain(id)
);

-- Continuer avec les inserts...

-- --------------------------------------------------------

--
-- Structure de la table method_sequencing
--

CREATE TABLE method_sequencing (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) DEFAULT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL,
  name_file VARCHAR(255) DEFAULT NULL,
  strain_id INTEGER DEFAULT NULL,
  type_file VARCHAR(255) DEFAULT NULL,
  date TIMESTAMP DEFAULT NULL,
  FOREIGN KEY (strain_id) REFERENCES strain(id)
);

-- --------------------------------------------------------

--
-- Structure de la table phenotype
--

CREATE TABLE phenotype (
  id SERIAL PRIMARY KEY,
  technique VARCHAR(255) DEFAULT NULL,
  mesure VARCHAR(255) DEFAULT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL,
  strain_id INTEGER DEFAULT NULL,
  phenotype_type_id INTEGER DEFAULT NULL,
  file_name VARCHAR(255) DEFAULT NULL,
  date TIMESTAMP DEFAULT NULL,
  FOREIGN KEY (strain_id) REFERENCES strain(id),
  FOREIGN KEY (phenotype_type_id) REFERENCES phenotype_type(id)
);

-- --------------------------------------------------------

--
-- Structure de la table storage
--

CREATE TABLE storage (
  id SERIAL PRIMARY KEY,
  room VARCHAR(255) DEFAULT NULL,
  rack VARCHAR(255) DEFAULT NULL,
  description VARCHAR(255) DEFAULT NULL,
  comment VARCHAR(255) DEFAULT NULL,
  strain_id INTEGER DEFAULT NULL,
  fridge VARCHAR(255) DEFAULT NULL,
  shelf VARCHAR(255) DEFAULT NULL,
  container_type VARCHAR(255) DEFAULT NULL,
  container_position VARCHAR(255) DEFAULT NULL,
  volume VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (strain_id) REFERENCES strain(id)
);

-- --------------------------------------------------------

--
-- Tables de liaison (many-to-many)
--

CREATE TABLE strain_collec (
  strain_id INTEGER NOT NULL,
  collec_id INTEGER NOT NULL,
  PRIMARY KEY (strain_id, collec_id),
  FOREIGN KEY (strain_id) REFERENCES strain(id) ON DELETE CASCADE,
  FOREIGN KEY (collec_id) REFERENCES collec(id) ON DELETE CASCADE
);

CREATE TABLE strain_plasmyd (
  strain_id INTEGER NOT NULL,
  plasmyd_id INTEGER NOT NULL,
  PRIMARY KEY (strain_id, plasmyd_id),
  FOREIGN KEY (strain_id) REFERENCES strain(id) ON DELETE CASCADE,
  FOREIGN KEY (plasmyd_id) REFERENCES plasmyd(id) ON DELETE CASCADE
);

CREATE TABLE strain_project (
  strain_id INTEGER NOT NULL,
  project_id INTEGER NOT NULL,
  PRIMARY KEY (strain_id, project_id),
  FOREIGN KEY (strain_id) REFERENCES strain(id) ON DELETE CASCADE,
  FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE CASCADE
);

CREATE TABLE strain_publication (
  strain_id INTEGER NOT NULL,
  publication_id INTEGER NOT NULL,
  PRIMARY KEY (strain_id, publication_id),
  FOREIGN KEY (strain_id) REFERENCES strain(id) ON DELETE CASCADE,
  FOREIGN KEY (publication_id) REFERENCES publication(id) ON DELETE CASCADE
);

-- --------------------------------------------------------

-- Index additionnels pour les performances

CREATE INDEX idx_strain_parent ON strain(parent_strain_id);
CREATE INDEX idx_strain_genotype ON strain(genotype_id);
CREATE INDEX idx_strain_depot ON strain(depot_id);
CREATE INDEX idx_strain_sample ON strain(prelevement_id);
CREATE INDEX idx_strain_user ON strain(created_by_id);
CREATE INDEX idx_drug_resistance_strain ON drug_resistance_on_strain(strain_id);
CREATE INDEX idx_drug_resistance_drug ON drug_resistance_on_strain(drug_resistance_id);
CREATE INDEX idx_method_sequencing_strain ON method_sequencing(strain_id);
CREATE INDEX idx_phenotype_strain ON phenotype(strain_id);
CREATE INDEX idx_phenotype_type ON phenotype(phenotype_type_id);
CREATE INDEX idx_storage_strain ON storage(strain_id);
CREATE INDEX idx_user_email ON "user"(email);

-- --------------------------------------------------------
-- Mise à jour de la table drug_resistance_on_strain
-- --------------------------------------------------------

-- Ajout de la colonne date (si elle n'existe pas déjà)
ALTER TABLE drug_resistance_on_strain 
ADD COLUMN IF NOT EXISTS date TIMESTAMP DEFAULT NULL;

-- --------------------------------------------------------
-- Mise à jour de la table user
-- --------------------------------------------------------

-- Renommer la colonne is_verified en is_access
ALTER TABLE "user" 
RENAME COLUMN is_verified TO is_access;

-- Si vous voulez que is_access accepte NULL (optionnel)
-- ALTER TABLE "user" ALTER COLUMN is_access DROP NOT NULL;

-- --------------------------------------------------------
-- Schéma complet mis à jour
-- --------------------------------------------------------


-- Mise à jour des données existantes
UPDATE "user" SET is_access = is_verified WHERE is_access IS NULL;

-- Données de la table "user" (mises à jour)
INSERT INTO "user" (id, email, firstname, lastname, roles, password, is_access, created_at) VALUES
(1, 'admin@test.fr', 'Baptiste', 'BERTRAND', '["ROLE_ADMIN"]', '$2y$13$KcrqG0c.6TXur02GyCfxdet4TKPvES/mMYmSJd7bbj3nF0bblD5LO', false, '2025-02-21 11:01:51'),
(2, 'intern@test.fr', 'nicolas', 'GAUDIN', '[]', '$2y$13$NSkc8NeSiCzahAnqq3b3fuc6oS6mnXBJCQ3ZlU1Oa95IvoQndQGHe', false, '2025-06-03 17:10:59'),
(3, 'search@test.fr', 'kelly', 'GOLDLUST', '["ROLE_SEARCH"]', '$2y$13$IhSEXtg4oVLcjSa57DQXEu./JVIRa.qw0YfB.lXIIYk1Pk0Itv0N.', false, '2025-07-23 23:00:18'),
(6, 'jerem@test.fr', 'Jeremy', 'Gony', '["ROLE_ADMIN"]', '$2y$13$INvr8a5HDeBifjWWzHi7gu.x5udhdh75CqcJ.1ZbT/qjJ8vv4kle2', false, '2025-11-07 06:26:05')
ON CONFLICT (id) DO NOTHING;

SELECT setval('user_id_seq', (SELECT MAX(id) FROM "user"));