USE AB_BORS;

UPDATE bors_keywords SET targets_count = (SELECT COUNT(*) FROM bors_keywords_index WHERE keyword_id = bors_keywords.id AND target_object_id<>target_container_object_id GROUP BY keyword_id);
UPDATE bors_keywords SET target_containers_count = (SELECT COUNT(*) FROM bors_keywords_index WHERE keyword_id = bors_keywords.id AND target_object_id=target_container_object_id GROUP BY keyword_id);


