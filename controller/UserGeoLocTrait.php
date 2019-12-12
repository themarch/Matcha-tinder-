<?php

trait UserGeoLocTrait
{
    private function createLocation($geo, $localisation)
    {
        $this->insert(
            'Profil',
            [
        'user_id' => $_SESSION['user_id'],
        'localisation' => $localisation,
        'longitude' => $geo['geoplugin_longitude'],
        'latitude' => $geo['geoplugin_latitude']
      ],
            ['user_id' => $_SESSION['user_id']]
      );
    }

    private function updateLocation($geo, $localisation)
    {
        $this->update(
            'Profil',
            [
            'localisation' => $localisation,
            'longitude' => $geo['geoplugin_longitude'],
            'latitude' => $geo['geoplugin_latitude']
          ],
            ['user_id' => $_SESSION['user_id']]
        );
    }

    public function getGeoLoc()
    {
        $geo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp'));
        $localisation = $geo['geoplugin_city'].', '.$geo['geoplugin_countryName'];
        if (!$this->fetch('Profil', ['user_id' => $_SESSION['user_id']])) {
            $this->createLocation($geo, $localisation);
        } else {
            $this->updateLocation($geo, $localisation);
        }
    }
}
