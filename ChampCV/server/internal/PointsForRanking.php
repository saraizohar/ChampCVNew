<?php

require_once('TasksGetter.php'); //for basePrice

class PointsForRanking
{
    public static function getPoints($openQuestion, $comments)
    {
        $basePoints = basePrice/3;
        $bunuseForOpen = $openQuestion  != null ? $basePoints / 4 : 0 ;
        $bunuseForComment = $comments   != null ? $basePoints / 4 : 0 ;

        return round($basePoints + $bunuseForOpen + $bunuseForComment);
    }
}

?>