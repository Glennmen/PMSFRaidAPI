<?php

namespace Scanner;

class RocketMap extends Scanner
{
    public function get_gyms_api($swLat, $swLng, $neLat, $neLng)
    {
        $conds = array();
        $params = array();

        $conds[] = "latitude > :swLat AND longitude > :swLng AND latitude < :neLat AND longitude < :neLng";
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

        $query = "SELECT gym.gym_id, 
        latitude, 
        longitude,
        name
        FROM gym
        LEFT JOIN gymdetails
        ON gym.gym_id = gymdetails.gym_id
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

        $query = "SELECT gym.gym_id, 
        latitude, 
        longitude,
        name,
        level AS raid_level, 
        pokemon_id AS raid_pokemon_id, 
        cp AS raid_pokemon_cp, 
        move_1 AS raid_pokemon_move_1, 
        move_2 AS raid_pokemon_move_2, 
        Unix_timestamp(Convert_tz(start, '+00:00', @@global.time_zone)) AS raid_start, 
        Unix_timestamp(Convert_tz(end, '+00:00', @@global.time_zone)) AS raid_end
        FROM gym
        LEFT JOIN gymdetails
        ON gym.gym_id = gymdetails.gym_id
        LEFT JOIN raid 
        ON gym.gym_id = raid.gym_id
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
