SELECT t.techstack_name as itemname, i.tech_id as itemid, 'Tech Stack' as itemtype, f.fav_id, f.fav_name FROM `favorites_item` i, favorites f, techstack_name t WHERE f.fav_id = i.fav_id AND i.tech_id = t.tech_id 

UNION 

SELECT p.product_name as itemname, i.product_id as itemid, 'Product' as itemtype, f.fav_id, f.fav_name FROM `favorites_item` i, favorites f, product p WHERE f.fav_id = i.fav_id AND i.product_id = p.prod_id;

SELECT t.techstack_name as itemname, i.tech_id as itemid, 'Tech Stack' as itemtype, f.fav_id, f.fav_name FROM `favorites_item` i, favorites f, techstack_name t WHERE f.fav_id = i.fav_id AND i.tech_id = t.tech_id UNION SELECT p.product_name as itemname, i.product_id as itemid, 'Product' as itemtype, f.fav_id, f.fav_name FROM `favorites_item` i, favorites f, product p WHERE f.fav_id = i.fav_id AND i.product_id = p.prod_id;