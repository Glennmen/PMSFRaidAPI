<?php

namespace Scanner;

class Monocle_Asner extends Monocle
{
    public function query_raids_api($conds, $params)
    {
        global $db;

        $query = "SELECT f.external_id AS gym_id, 
        f.lat AS latitude, 
        f.lon AS longitude,
        raid_level,
        pokemon_id AS raid_pokemon_id,
        cp AS raid_pokemon_cp,
        raid_start,
        raid_end,
        move_1 AS raid_pokemon_move_1,
        move_2 AS raid_pokemon_move_2
        FROM (SELECT f.id,
          f.external_id,
          f.lat,
          f.lon, 
          MAX(r.id) AS raid_id
          FROM   forts f
          LEFT JOIN raid_info r ON r.fort_id = f.id
          GROUP  BY f.id) f
        LEFT JOIN raid_info r ON r.id = f.raid_id
        WHERE :conditions";

        $query = str_replace(":conditions", join(" AND ", $conds), $query);
        $gyms = $db->query($query, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $data = array();
        $i = 0;

        foreach ($gyms as $gym) {
            $gym["latitude"] = floatval($gym["latitude"]);
            $gym["longitude"] = floatval($gym["longitude"]);
            $data[] = $gym;

            unset($gyms[$i]);
            $i++;
        }
        return $data;
    }
}
