Слишком ресурсоёмко
--UPDATE posts SET have_answers = NULL;
--UPDATE posts SET have_answers = 0 WHERE id IN (SELECT id FROM posts WHERE answer_to > 0);
--UPDATE posts p0 SET have_answers = SUM(SELECT have_answers p1 FROM posts WHERE p1.answer_to = p0.id) WHERE