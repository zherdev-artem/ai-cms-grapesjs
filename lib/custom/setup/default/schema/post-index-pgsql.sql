--
-- PostgreSQL specific database definitions
--

CREATE INDEX "idx_mspostindte_content" ON "mshop_post_index_text" USING GIN (to_tsvector('english', "content"));
