USE AB_FORUMS;

-- Запишем голоса за сообщения

TRUNCATE TABLE user_relations;

CREATE TEMPORARY TABLE tmp_vote_relations
	SELECT
		user_id AS from_user_id,
		target_user_id AS to_user_id,
		SUM(IF(score > 0, 1, 0)) AS pos,
		SUM(IF(score < 0, 1, 0)) AS neg
	FROM
		AB_BORS.bors_thumb_votes
	GROUP BY user_id, target_user_id;

ALTER TABLE tmp_vote_relations
	ADD UNIQUE INDEX (from_user_id, to_user_id);

INSERT IGNORE INTO user_relations (from_user_id, to_user_id)
	SELECT
		from_user_id,
		to_user_id
	FROM tmp_vote_relations;

UPDATE user_relations r
	LEFT JOIN tmp_vote_relations v
		ON r.from_user_id = v.from_user_id
			AND r.to_user_id = v.to_user_id
SET r.votes_plus = pos,	r.votes_minus = neg;

-- Запишем голоса за репутацию

CREATE TEMPORARY TABLE tmp_reputation_relations
	SELECT
		voter_id AS from_user_id,
		user_id AS to_user_id,
		SUM(IF(score > 0, 1, 0)) AS pos,
		SUM(IF(score < 0, 1, 0)) AS neg
	FROM
		USERS.reputation_votes
	WHERE is_deleted = 0
	GROUP BY voter_id, user_id;

ALTER TABLE tmp_reputation_relations
	ADD UNIQUE INDEX (from_user_id, to_user_id);

INSERT IGNORE INTO user_relations (from_user_id, to_user_id)
	SELECT
		from_user_id,
		to_user_id
	FROM tmp_reputation_relations;

UPDATE user_relations r
	LEFT JOIN tmp_reputation_relations v
		ON r.from_user_id = v.from_user_id
			AND r.to_user_id = v.to_user_id
SET r.reputations_plus = pos,	r.reputations_minus = neg;

UPDATE user_relations SET score =
	votes_plus/(votes_plus+votes_minus+1) * SQRT(votes_plus)
		- votes_minus/(votes_plus+votes_minus+1) * SQRT(votes_minus)
	+ 15 * ( reputations_plus/(reputations_plus+reputations_minus+1) * SQRT(reputations_plus)
		- reputations_minus/(reputations_plus+reputations_minus+1) * SQRT(reputations_minus))
;

UPDATE user_relations SET
	from_user_name = (SELECT username FROM users u WHERE u.id = from_user_id),
	to_user_name   = (SELECT username FROM users u WHERE u.id = to_user_id);

-- INSERT IGNORE INTO user_relations (from_user_id, from_user_name) SELECT to_user_id, to_user_name FROM user_relations;
-- INSERT IGNORE INTO user_relations (to_user_id, to_user_name) SELECT from_user_id, from_user_name FROM user_relations;


