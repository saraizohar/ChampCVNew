<?php

require_once('../DBconnection/reliability.php');

class PricingAlg
{
    /*
     * compute CDF of N(0,1);
     * CREDIT https://www.johndcook.com/blog/cpp_phi/
     */
    private static function phi($x, $avrage)
    {
        $x = $x - $avrage;

        // constants
        $a1 =  0.254829592;
        $a2 = -0.284496736;
        $a3 =  1.421413741;
        $a4 = -1.453152027;
        $a5 =  1.061405429;
        $p  =  0.3275911;

        // Save the sign of x
        $sign = $x < 0 ? -1 : 1;

        $x = abs($x) /sqrt(2.0);

        // A&S formula 7.1.26
        $t = 1.0/(1.0 + $p * $x);
        $y = 1.0 - ((((($a5 * $t + $a4) * $t) + $a3) * $t + $a2) * $t + $a1) * $t * exp(-$x * $x);

        return 0.5*(1.0 + $sign * $y);
    }

    private static function getPrice($stats)
    {
        $userTotGrade = 0;
        $croudTotGrade = 0;

        foreach ($stats as $grade)
        {
            $userTotGrade  += (float) $grade['userAvg'];
            $croudTotGrade += (float) $grade['crowdAvg'];
        }

        $userAvgGrade = $userTotGrade / count($stats);
        $croudAvgGrade = $croudTotGrade / count($stats);

        // = pr (x < userAvrage) - pr (x <  crowdAvg)
        $scaleFactor = PricingAlg::phi($userAvgGrade, $croudAvgGrade) - 0.5;

        $price = round(basePrice + basePrice * $scaleFactor);
        return $price;
    }
    
    private static function getStats($task)
    {
        
        $statistic= reliability::get_ranks_results_array($task['id']);
        if (key($statistic) === 'error_message')
            return $statistic;
        if ($statistic['numOfRankers'] == 0)
        {
            foreach ($statistic['gradePerQuestion'] as &$val)
            {
                $val['userAvg'] = 2.5;
                $val['crowdAvg'] = 2.5;
            }
        }
        return $statistic['gradePerQuestion'];
    }

    public static function calcPriceForRecruter(&$tasks)
    {   
        
        foreach ($tasks as &$task)
        {
            $stats = PricingAlg::getStats($task);
            if (key($stats) === 'error_message')
                return $stats;

            $task['price'] = PricingAlg::getPrice($stats);
        }

        return null;
    }
}


?>