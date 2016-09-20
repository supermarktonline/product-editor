<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 1/19/16
 * Time: 8:08 PM
 */


$stmt = $db->prepare("select tg.id as group_id,tg.gs1_attribute_type_code as group_code,tg.muid as group_muid, tg.name_de as group_name, tg.definition_en as group_def_en, tg.definition_de as group_def_de, t.id as tag_id, t.gs1_attribute_value_code as tag_code, t.muid as tag_muid, t.name_de as tag_name_de, t.name_at as tag_name_at, t.definition_en as tag_def_en, t.definition_de as tag_def_de from category as c,category_tag as ct, tag as t INNER JOIN taggroup as tg ON t.taggroup=tg.id where c.brick_code = :brick_code and ct.category_id = c.gid and t.id = ct.tag_id AND t.gs1_attribute_value_code IS NOT NULL");
$stmt->bindValue(":brick_code",$_REQUEST["brick_code"]);

if(!$stmt->execute()) {
    echo "SQL Failure: ".$db->errorInfo()[2]."."; die;
} else {

    $result = $stmt->fetchAll();

    $tree = array();

    foreach($result as $res) {
        $group_id = $res["group_id"];
        if(!isset($tree[$group_id])) {
            $tree[$group_id] = array();
            $tree[$group_id]["id"] = $group_id;
            $tree[$group_id]["code"] = $res["group_code"];
            $tree[$group_id]["muid"] = $res["group_muid"];
            $tree[$group_id]["name"] = $res["group_name"];
            $tree[$group_id]["def_en"] = $res["group_def_en"];
            $tree[$group_id]["def_de"] = $res["group_def_de"];
            $tree[$group_id]["tags"] = array();
        }

        array_push($tree[$group_id]["tags"],$res);
    }

    echo json_encode($tree); die;
}