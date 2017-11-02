<?php

namespace Scanner;

class Monocle extends Scanner
{
    public function get_gyms_api($swLat, $swLng, $neLat, $neLng)
    {
        $conds = array();
        $params = array();

        $conds[] = "f.lat > :swLat AND f.lon > :swLng AND f.lat < :neLat AND f.lon < :neLng";
        $params[':swLat'] = $swLat;
        $params[':swLng'] = $swLng;
        $params[':neLat'] = $neLat;
        $params[':neLng'] = $neLng;

        global $sendRaidData;
        if (!$sendRaidData)
            return $this->query_gyms_api($conds, $params);
        else
            return $this->query_raids_api($conds, $params);
    }

    public function query_gyms_api($conds, $params)
    {
        global $db;

        $query = "SELECT f.external_id AS gym_id, 
        f.lat AS latitude, 
        f.lon AS longitude
        FROM forts f
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

    public function query_raids_api($conds, $params)
    {
        global $db;

        $query = "SELECT f.external_id AS gym_id, 
        f.lat AS latitude, 
        f.lon AS longitude,
        level AS raid_level,
        pokemon_id AS raid_pokemon_id,
        time_battle AS raid_start,
        time_end AS raid_end,
        move_1 AS raid_pokemon_move_1,
        move_2 AS raid_pokemon_move_2
        FROM (SELECT f.id,
          f.external_id,
          f.lat,
          f.lon,
          MAX(r.id) AS raid_id
          FROM   forts f
          LEFT JOIN raids r ON r.fort_id = f.id
          GROUP  BY f.id) f
        LEFT JOIN raids r ON r.id = f.raid_id
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
