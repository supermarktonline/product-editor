SET search_path = inetshop_backend;
SELECT DISTINCT tg.c_muid,str.c_name FROM tbl_taggrouping AS tg, tbl_taggrouping_taggroupingstrings AS con, tbl_taggroupingstrings AS str
WHERE con.c_taggroupings_id = tg.c_id AND con.c_strings_id = str.c_id