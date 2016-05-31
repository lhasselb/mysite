<?php
class LocationExtension extends DataExtension
{
    public function updateBasicMap($map, $autozoom) {
        $map->setMapType('satellite');

    }
}
